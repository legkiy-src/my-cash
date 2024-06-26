<?php

namespace App\Services\Account;

use App\Repositories\AccountRepository;
use App\Repositories\CurrencyRepository;
use App\Repositories\ExpenseRepository;
use App\Repositories\RevenueRepository;
use App\Services\BaseService;
use App\Services\Expense\Exceptions\NotEnoughMoneyException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AccountService extends BaseService
{
    private AccountRepository $accountRepository;
    private CurrencyRepository $currencyRepository;
    private RevenueRepository $revenueRepository;
    private ExpenseRepository $expenseRepository;

    public function __construct(
        AccountRepository $accountRepository,
        CurrencyRepository $currencyRepository,
        RevenueRepository $revenueRepository,
        ExpenseRepository $expenseRepository
    ) {
        $this->accountRepository = $accountRepository;
        $this->currencyRepository = $currencyRepository;
        $this->revenueRepository = $revenueRepository;
        $this->expenseRepository = $expenseRepository;
    }

    public function getAccounts(): Collection
    {
        $userId = $this->getUserId();

        return $this->accountRepository->getAccounts($userId);
    }

    public function getAccountById(int $id): array
    {
        $userId = $this->getUserId();
        $currencies = $this->currencyRepository->getCurrenciesByUserId($userId);
        $account = $this->accountRepository->getAccountById($userId, $id);

        return [
            'account' => $account?->toArray(),
            'currencies' => $currencies?->toArray()
        ];
    }

    public function updateAccount(
        int $id,
        ?string $name,
        ?int $balance,
        ?int $currency,
        ?string $description
    ): int {
        $userId = $this->getUserId();

        return $this->accountRepository->updateAccount(
            $userId,
            $id,
            $name,
            $balance * 100,
            $currency,
            $description
        );
    }

    public function deleteAccount(int $id): mixed
    {
        $userId = $this->getUserId();

        return DB::transaction(
            function () use ($userId, $id) {
                $this->revenueRepository->deleteRevenueByAccountId($userId, $id);
                $this->expenseRepository->deleteExpenseByAccountId($userId, $id);

                return $this->accountRepository->deleteAccount($userId, $id);
            }
        );
    }

    public function createAccount(string $name, int $balance, int $currency, ?string $description): bool
    {
        $userId = $this->getUserId();

        return $this->accountRepository->createAccount($userId, $name, $balance * 100, $currency, $description);
    }

    public function updateBalance(int $id, int $balance): bool
    {
        $userId = $this->getUserId();

        return $this->accountRepository->updateBalance($userId, $id, $balance * 100);
    }

    /**
     * @param  int  $id
     * @param  int  $sum  - сумма в копейках
     * @return bool
     */
    public function balanceIncrement(int $id, int $sum): bool
    {
        $userId = $this->getUserId();
        $account = $this->accountRepository->getAccountById($userId, $id);
        $balance = $account->balance + $sum;

        return $this->accountRepository->updateBalance($userId, $id, $balance);
    }

    /**
     * @param  int  $id
     * @param  int  $sum  - сумма в копейках
     * @return bool
     * @throws NotEnoughMoneyException
     */
    public function balanceDecrement(int $id, int $sum): bool
    {
        $userId = $this->getUserId();

        $account = $this->accountRepository->getAccountById($userId, $id);

        if ($account->balance < $sum) {
            throw new NotEnoughMoneyException();
        }

        $balance = $account->balance - $sum;

        return $this->accountRepository->updateBalance($userId, $id, $balance);
    }
}
