<?php

declare(strict_types=1);

namespace Neg\CustomerAccountEnhancement\Services;

use Shopware\Core\System\SystemConfig\SystemConfigService;

class PluginConfigService
{
    public function __construct(
        private readonly SystemConfigService $systemConfigService,
    ) {
    }

    public function getPluginConfiguration(): array
    {
        return $this->systemConfigService->get('CustomerAddressConfig.config', null) ?: [];
    }

    public function getCustomerGroup(): bool|string
    {
        $config = $this->getPluginConfiguration();

        return $config['customerGroup'] ?? false;
    }
}
