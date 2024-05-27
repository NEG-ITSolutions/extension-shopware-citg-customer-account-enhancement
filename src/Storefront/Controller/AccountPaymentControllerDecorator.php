<?php

declare(strict_types=1);

namespace Neg\CustomerAccountEnhancement\Storefront\Controller;

use CustomerAddressConfig\Services\PluginConfigService;
use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\AccountPaymentController;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class AccountPaymentControllerDecorator extends StorefrontController
{
    public function __construct(
        private readonly AccountPaymentController $innerService,
        private readonly PluginConfigService $configService,
    ) {
    }

    public function savePayment(
        RequestDataBag $requestDataBag,
        SalesChannelContext $context,
        CustomerEntity $customer,
    ): Response {
        $myConfigValue = $this->configService->getCustomerGroup();

        if ($myConfigValue === $customer->getGroupId()) {
            $this->addFlash(self::DANGER, $this->trans('account.paymentUpdateBlocked'));

            return new RedirectResponse($this->generateUrl('frontend.account.payment.page'));
        }

        return $this->innerService->savePayment($requestDataBag, $context, $customer);
    }

    public function paymentOverview(Request $request, SalesChannelContext $context): Response
    {
        return $this->innerService->paymentOverview($request, $context);
    }

    private function getCustomerGroupName(string $groupId, Context $context): ?string
    {
        // Get the customer group repository
        $customerGroupRepository = $this->container->get('customer_group.repository');

        // Fetch the customer group by ID
        $customerGroup = $customerGroupRepository->search(new Criteria([$groupId]), $context)->first();

        // Return the customer group name
        return $customerGroup ? $customerGroup->getName() : null;
    }
}
