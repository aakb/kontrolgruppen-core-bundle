<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\CVR;

use ItkDev\AzureKeyVault\Authorisation\VaultToken;
use ItkDev\AzureKeyVault\Exception\TokenException;
use ItkDev\AzureKeyVault\KeyVault\VaultSecret;
use ItkDev\Serviceplatformen\Certificate\AzureKeyVaultCertificateLocator;
use ItkDev\Serviceplatformen\Certificate\Exception\CertificateLocatorException;
use ItkDev\Serviceplatformen\Request\InvocationContextRequestGenerator;
use ItkDev\Serviceplatformen\Service\OnlineService;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

/**
 * Class ServiceplatformenServiceFactory.
 */
class ServiceplatformenServiceFactory
{
    /**
     * Factory for CVR service.
     *
     * @param ClientInterface         $httpClient
     * @param RequestFactoryInterface $requestFactory
     * @param string                  $azureTenantId
     * @param string                  $azureApplicationId
     * @param string                  $azureClientSecret
     * @param string                  $azureKeyVaultName
     * @param string                  $azureKeyVaultSecret
     * @param string                  $azureKeyVaultSecretVersion
     * @param string                  $serviceplatformenServiceAgreementUuid
     * @param string                  $serviceplatformenUserSystemUuid
     * @param string                  $serviceplatformenUserUuid
     * @param string                  $serviceContractFilename
     * @param string                  $serviceEndpoint
     * @param string                  $serviceUuid
     *
     * @return OnlineService
     *
     * @throws CvrException
     */
    public static function createOnlineService(ClientInterface $httpClient, RequestFactoryInterface $requestFactory, string $azureTenantId, string $azureApplicationId, string $azureClientSecret, string $azureKeyVaultName, string $azureKeyVaultSecret, string $azureKeyVaultSecretVersion, string $serviceplatformenServiceAgreementUuid, string $serviceplatformenUserSystemUuid, string $serviceplatformenUserUuid, string $serviceContractFilename, string $serviceEndpoint, string $serviceUuid)
    {
        try {
            $vaultToken = new VaultToken($httpClient, $requestFactory);
            $token = $vaultToken->getToken(
                $azureTenantId,
                $azureApplicationId,
                $azureClientSecret
            );
        } catch (TokenException $e) {
            throw new CvrException($e->getMessage(), $e->getCode());
        }

        $vault = new VaultSecret(
            $httpClient,
            $requestFactory,
            $azureKeyVaultName,
            $token->getAccessToken()
        );

        $certificateLocator = new AzureKeyVaultCertificateLocator(
            $vault,
            $azureKeyVaultSecret,
            $azureKeyVaultSecretVersion
        );

        try {
            $pathToCertificate = $certificateLocator->getAbsolutePathToCertificate();
        } catch (CertificateLocatorException $e) {
            throw new CvrException($e->getMessage(), $e->getCode());
        }

        $options = [
            'local_cert' => $pathToCertificate,
            'passphrase' => $certificateLocator->getPassphrase(),
            'location' => $serviceEndpoint,
        ];

        if (!realpath($serviceContractFilename)) {
            throw new CprException(sprintf('The path (%s) to the service contract is invalid.', $serviceContractFilename));
        }

        try {
            $soapClient = new \SoapClient($serviceContractFilename, $options);
        } catch (\SoapFault $e) {
            throw new CvrException($e->getMessage(), $e->getCode());
        }

        $requestGenerator = new InvocationContextRequestGenerator(
            $serviceplatformenServiceAgreementUuid,
            $serviceplatformenUserSystemUuid,
            $serviceUuid,
            $serviceplatformenUserUuid
        );

        return new OnlineService($soapClient, $requestGenerator);
    }
}
