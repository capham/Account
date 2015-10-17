<?php
namespace AppBundle\Controller;

use AppBundle\Domain\Account\Account;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Options;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Huong Le <tonyle.microsoft@gmail.com>
 */
class AccountController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Retrieves account information
     *
     * @QueryParam(name="accountID", requirements = "\d+", strict=true, description="id of account", nullable=false)
     *
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     * @return array
     * @author Huong Le <tonyle.microsoft@gmail.com>
     * @Rest\View
     * @Get("", name = "info", options = {"method_prefix" = false})
     */
    public function getAccountAction(ParamFetcherInterface $paramFetcher)
    {
        $accoutId = $paramFetcher->get('accountID');
        $accountRepo = $this->get('account.repository');
        $account = $accountRepo->find($accoutId);
        if (!$account instanceof Account) {
            throw new NotFoundHttpException('Account not exists');
        }
        $account = $accountRepo->getFinalResultByJMSGroup($account, 'account');
        return ['data' => $account];
    }

    /**
     * Spends some money
     *
     * @QueryParam(name="accountID", requirements = "\d+", strict=true, description="id of account", nullable=false)
     * @QueryParam(name="amount", requirements = "[0-9.]+", strict=true, description="the amount being spent", nullable=false)
     *
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     * @return array
     * @author Huong Le <tonyle.microsoft@gmail.com>
     * @Rest\View
     * @Get("/spend", name = "spend", options = {"method_prefix" = false})
     */
    public function spendAction(ParamFetcherInterface $paramFetcher)
    {
        $accoutId = $paramFetcher->get('accountID');
        $amout = $paramFetcher->get('amount');
        if (floatval($amout) <= 0) {
            throw new BadRequestHttpException('Amount must be greater than 0');
        }
        $accountRepo = $this->get('account.repository');
        $account = $accountRepo->find($accoutId);
        if (!$account instanceof Account) {
            throw new NotFoundHttpException('Account not exists');
        }
        $account = $accountRepo->spend($account, $amout);
        if (!$account) {
            throw new BadRequestHttpException('You should not be able to spend more than is available ');
        }
        $message = \Swift_Message::newInstance()
            ->setSubject('New balance')
            ->setFrom('admin@example.com')
            ->setTo($account->get('email'))
            ->setBody('New balance: ' . $account->get('currentBalance'))
            ->setContentType("text/html");
        $this->get('mailer')->send($message);
        $account = $accountRepo->getFinalResultByJMSGroup($account, 'detail');
        return ['data' => $account];
    }

    /**
     * Get the current account balance
     *
     * @QueryParam(name="accountID", requirements = "\d+", strict=true, description="id of account", nullable=false)
     *
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     * @return array
     * @author Huong Le <tonyle.microsoft@gmail.com>
     * @Rest\View
     * @Get("/balance", name = "balance", options = {"method_prefix" = false})
     */
    public function getBalanceAction(ParamFetcherInterface $paramFetcher)
    {
        $accoutId = $paramFetcher->get('accountID');
        $accountRepo = $this->get('account.repository');
        $account = $accountRepo->find($accoutId);
        if (!$account instanceof Account) {
            throw new NotFoundHttpException('Account not exists');
        }
        $account = $accountRepo->getFinalResultByJMSGroup($account, 'balance');
        return ['data' => $account];
    }
}
