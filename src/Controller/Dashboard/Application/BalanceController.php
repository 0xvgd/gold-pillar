<?php

namespace App\Controller\Dashboard\Application;

use App\Controller\Traits\DatatableTrait;
use App\Entity\Finance\CompanyAccount;
use App\Entity\Finance\CompanyTransaction;
use App\Entity\Helper\TableValue;
use App\Form\Dashboard\Application\CreateNoteType;
use App\Form\Dashboard\Application\CreateTransferType;
use App\Form\Dashboard\Asset\AssetNoteItemType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("dashboard/application/balance")
 */
class BalanceController extends AbstractController
{
    use DatatableTrait;

    const ACTION = 'action';

    /**
     * @Route("/", name="dashboard_application_balance_index")
     */
    public function index(
        Request $request,
        EntityManagerInterface $em
    ) {
        // $createTransferForm = $this->createForm(CreateTransferType::class, [
        //     self::ACTION => $this->generateUrl('dashboard_application_balance_dotransfer'),
        // ]);

        // $createCreditNoteForm = $this->createForm(CreateNoteType::class, [
        //     self::ACTION => $this->generateUrl('dashboard_application_balance_docreditnote'),
        // ]);

        // $createDebitNoteForm = $this->createForm(CreateNoteType::class, [
        //     self::ACTION => $this->generateUrl('dashboard_application_balance_dodebitnote'),
        // ]);

        $tab = $request->get('tab');
        $companyAccount = $this->getCompanyAccount();
        $application = null;
        $errors = null;
        $balance = 0;

        return $this->render('dashboard/application/balance/index.html.twig', [
            'errors' => $errors,
            'tab' => $tab,
            'balance' => $balance,
            'companyTransactions' => $companyAccount->getTransactions(),
            'createTransferForm' => $createTransferForm->createView(),
            'createCreditNoteForm' => $createCreditNoteForm->createView(),
            'createDebitNoteForm' => $createDebitNoteForm->createView(),
        ]);
    }

    /**
     * @Route("/search.json", name="dashboard_application_balance_search", methods={"GET"})
     */
    public function search(Request $request, EntityManagerInterface $em)
    {
        $companyAccount = $this->getCompanyAccount();

        $qb = $em->createQueryBuilder()
            ->select('e')
            ->from(CompanyTransaction::class, 'e')
            ->andWhere('e.account = :account')
            ->setParameter('account', $companyAccount)
            ->orderBy('e.id', 'DESC');

        $query = $qb
            ->getQuery();

        return $this->dataTable($request, $query, false);
    }

    /**
     * @Route("/dotransfer", name="dashboard_application_balance_dotransfer")
     */
    public function doTransfer(
        Request $request,
        EntityManagerInterface $em
    ) {
        $errors = null;
        $transactionId = md5(uniqid('transf'));
        $form = $this->createForm(CreateTransferType::class)
            ->handleRequest($request);
        $accountFromType = $form->get('accountFromType')->getData();
        $accountToType = $form->get('accountToType')->getData();
        $amount = $form->get('amount')->getData();
        $dateRef = $form->get('dateRef')->getData();
        $response = new Response();

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $this->transf($accountFromType, $form, $transactionId, $amount, $dateRef, true);
                $this->transf($accountToType, $form, $transactionId, $amount, $dateRef);

                $em->flush();

                $application = $this->getCurrentApplication();
                $balance = $em->getRepository(CompanyAccount::class)->getBalance($application);

                $response->setContent(json_encode([
                    'data' => 'Item saved successfully.',
                    'balance' => $balance,
                    'trigger' => '#modal-transfer',
                ]));
            } else {
                $errors = $form->getErrors(true);
                $response->setContent(json_encode([
                    'errors' => $errors,
                ]));
            }
        } catch (Exception $e) {
            $response->setContent(json_encode([
                'error' => $e->getMessage(),
            ]));
        }

        $response->headers->set('Content-Type', 'application/json');

        return  $response;
    }

    private function transf($accountType, $form, $transactionId, $amount, $dateRef, $debit = false)
    {
        $type = str_replace(['From', 'To'], '', $accountType);
        $em = $this->getDoctrine()->getManager();
        $entity = null;
        $accountService = null;
        $transactionTransfer = $em
            ->getRepository(TableValue::class)
            ->find(TableValue::BALANCE_TRANSACTION_TYPE_TRANSFER);

        if ('company' === $type) {
            $account = $this->getCompanyAccount();
            $accountService = $this->companyAccountService;
        } else {
            $account = $form->get("{$accountType}Account")->getData();
            switch ($type) {
                case 'asset':
                    $entity = $account->getAsset();
                    $accountService = $this->assetAccountService;
                    break;
                case 'project':
                    $entity = $account->getProject();
                    $accountService = $this->projectAccountService;
                    break;
                case 'property':
                    $entity = $account->getProperty();
                    $accountService = $this->propertyAccountService;
                    break;
            }
        }

        $transaction = [
            'transactionId' => $transactionId,
            'amount' => $amount,
            'description' => 'Transfer',
            'type' => $transactionTransfer,
            'entity' => $entity,
            'account' => $account,
            'user' => $this->getUser(),
            'dateRef' => $dateRef,
            'fee' => 0,
            'refValue' => 0,
        ];

        $this->doTransaction(
            $transaction,
            $accountService,
            $em,
            $debit
        );
    }

    /**
     * @Route("/docreditnote", name="dashboard_application_balance_docreditnote")
     */
    public function doCreditNote(
        Request $request
    ) {
        return $this->doNote(
            '#modal-credit-note',
            $request,
            false
        );
    }

    /**
     * @Route("/dodebitnote", name="dashboard_application_balance_dodebitnote")
     */
    public function doDebitNote(
        Request $request
    ) {
        return $this->doNote(
            '#modal-debit-note',
            $request,
            true
        );
    }

    /**
     * @Route("/docreditnote-asset", name="dashboard_application_balance_docreditnote_asset")
     */
    public function doCreditNoteAsset(
        Request $request
    ) {
        return $this->doNote(
            '#modal-credit-note-asset',
            $request,
            false
        );
    }

    /**
     * @Route("/dodebitnote-asset", name="dashboard_application_balance_dodebitnote_asset")
     */
    public function doDebitNoteAsset(
        Request $request
    ) {
        return $this->doNote(
            '#modal-debit-note-asset',
            $request,
            true
        );
    }

    public function doTransaction(
        $transaction,
        $service,
        EntityManagerInterface $em,
        $debit = false
    ) {
        $service->doTransaction($transaction, $em, $debit);
    }

    private function doNote(
        string $trigger,
        Request $request,
        $debit = false
    ) {
        $companyTransactionType = null;
        $transactionId = md5(uniqid('transf'));
        $em = $this->getDoctrine()->getManager();
        $accountService = null;
        $response = new Response();
        $companyTransactionType = $this->getCompanyTransactionType($debit, $em);
        $companyAccount = $this->getCompanyAccount();

        if ('#modal-credit-note-asset' === $trigger || '#modal-debit-note-asset' === $trigger) {
            $form = $this->createForm(AssetNoteItemType::class)
                ->handleRequest($request);
        } else {
            $form = $this->createForm(CreateNoteType::class)
                ->handleRequest($request);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $accountToType = $form->get('accountType')->getData();
            $amount = $form->get('amount')->getData();
            $description = $form->get('description')->getData();
            $dateRef = $form->get('dateRef')->getData();
            $entityTo = null;
            $accountTo = null;

            try {
                if ('company' === $accountToType) {
                    $accountTo = $companyAccount;
                    $type = $companyTransactionType;
                    $entityTo = null;
                    $accountService = $this->companyAccountService;
                } else {
                    $accountTo = $form->get("{$accountToType}Account")->getData();
                    $type = $form->get("{$accountToType}TransactionType")->getData();
                    switch ($accountToType) {
                        case 'asset':
                            $entityTo = $accountTo->getAsset();
                            $accountService = $this->assetAccountService;
                            break;
                        case 'project':
                            $entityTo = $accountTo->getProject();
                            $accountService = $this->projectAccountService;
                            break;
                        case 'property':
                            $entityTo = $accountTo->getProperty();
                            $accountService = $this->propertyAccountService;
                            break;
                    }
                }

                $transaction = [
                    'transactionId' => $transactionId,
                    'amount' => $amount,
                    'description' => $description,
                    'type' => $type,
                    'entity' => $entityTo,
                    'account' => $accountTo,
                    'user' => $this->getUser(),
                    'dateRef' => $dateRef,
                    'fee' => 0,
                    'refValue' => 0,
                ];

                $this->doTransaction(
                    $transaction,
                    $accountService,
                    $em,
                    $debit
                );

                $em->flush();

                $application = $this->getCurrentApplication();
                $balance = $em->getRepository(CompanyAccount::class)->getBalance($application);

                $response->setContent(json_encode([
                    'data' => 'Item saved successfully.',
                    'balance' => $balance,
                    'trigger' => $trigger,
                ]));
            } catch (Exception $e) {
                $response->setContent(json_encode([
                    'error' => $e->getMessage(),
                ]));
            }
        } else {
            $errors = $form->getErrors(true);
            $response->setContent(json_encode([
                'errors' => $errors,
            ]));
        }

        $response->headers->set('Content-Type', 'application/json');

        return  $response;
    }

    private function getCompanyTransactionType(bool $debit, EntityManagerInterface $em)
    {
        if ($debit) {
            $companyTransactionType = $em
                ->getRepository(TableValue::class)
                ->find(TableValue::BALANCE_TRANSACTION_TYPE_DEBIT_NOTE);
        } else {
            $companyTransactionType = $em
                ->getRepository(TableValue::class)
                ->find(TableValue::BALANCE_TRANSACTION_TYPE_CREDIT_NOTE);
        }

        return $companyTransactionType;
    }

    private function getCompanyAccount()
    {
        $em = $this->getDoctrine()->getManager();
        $application = $this->getCurrentApplication();

        return $em->getRepository(CompanyAccount::class)->findOneBy([
            'application' => $application,
        ]);
    }
}
