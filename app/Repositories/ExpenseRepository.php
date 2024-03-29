<?php

namespace App\Repositories;

use App\Models\Expense;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ExpenseRepository
{
    public function getExpenses(int $userId): Collection
    {
        return Expense::with(['article', 'account'])
            ->where('user_id', $userId)
            ->get();
    }

    public function createExpense(
        int $userId,
        int $operationId,
        int $accountId,
        int $articleId,
        int $totalSum,
        ?string $description
    ): int {
        $expense = new Expense();

        $expense->user_id = $userId;
        $expense->operation_id = $operationId;
        $expense->account_id = $accountId;
        $expense->article_id = $articleId;
        $expense->total_sum = $totalSum;
        $expense->description = $description;

        $expense->save();

        return $expense->id;
    }

    public function getExpenseById(int $userId, int $id): ?Model
    {
        return Expense::query()
            ->where('user_id', $userId)
            ->where('id', $id)
            ->first();
    }

    public function updateExpense(
        int $id,
        int $userId,
        int $accountId,
        int $articleId,
        int $totalSum,
        ?string $description
    ): bool {
        $expense = Expense::query()
            ->where('user_id', '=', $userId)
            ->where('id', '=', $id)
            ->first();

        $expense->account_id = $accountId;
        $expense->article_id = $articleId;
        $expense->total_sum = $totalSum;
        $expense->description = $description;

        return $expense->save();
    }

    public function deleteExpense(int $userId, int $id): mixed
    {
        return Expense::query()
            ->where('user_id', '=', $userId)
            ->where('id', '=', $id)
            ->delete();
    }

    public function deleteExpenseByAccountId(int $userId, int $accountId): mixed
    {
        return Expense::query()
            ->where('user_id', '=', $userId)
            ->where('account_id', '=', $accountId)
            ->delete();
    }

    public function deleteExpenseByArticleId(int $userId, int $articleId): mixed
    {
        return Expense::query()
            ->where('user_id', '=', $userId)
            ->where('article_id', '=', $articleId)
            ->delete();
    }

    public function getSumGroupByAccountId(int $userId, int $articleId): Collection
    {
        return Expense::query()
            ->select(
                'user_id',
                'article_id',
                'account_id',
                DB::raw('sum(total_sum) as sum')
            )
            ->groupBy('user_id', 'article_id', 'account_id', 'total_sum')
            ->having('user_id', '=', $userId)
            ->having('article_id', '=', $articleId)
            ->get();
    }
}
