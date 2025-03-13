<?php

namespace App\SimpsonsQuotes;

use App\Models\SimpsonsQuote as SimpsonsQuoteModel;
use Exception;
use Illuminate\Support\Facades\DB as DbFacade;


class SimpsonsQuotes
{
    protected int $maxAllowedQuotesCount = 5;
    protected array $expectedFields = ['quote', 'character', 'image', 'characterDirection'];
    protected array $quotes = [];
    protected SimpsonsQuoteModel $simpsonsQuoteModel;
    protected SimpsonsQuotesFetcher $simpsonsQuotesFetcher;
    protected DbFacade $dbFacade;

    public function __construct(SimpsonsQuoteModel $simpsonsQuoteModel, SimpsonsQuotesFetcher $simpsonsQuotesFetcher, DbFacade $dbFacade)
    {
        $this->simpsonsQuoteModel = $simpsonsQuoteModel;
        $this->simpsonsQuotesFetcher = $simpsonsQuotesFetcher;
        $this->dbFacade = $dbFacade;
    }

    public function composeQuotesForDisplay(): array
    {
        $this->loadQuotesFromStorage();
        $countOfQuotesToFetch = $this->calculateCountOfQuotesToFetchFromSimpsonsApi();
        $newQuotes = $this->fetchQuotesFromSimpsonsApi($countOfQuotesToFetch);
        $this->addQuotesToStorage(...$newQuotes);
        $this->pruneObsoleteQuotes();

        return $this->quotes;
    }

    protected function loadQuotesFromStorage(): void
    {
        $quotes = $this->simpsonsQuoteModel::orderBy('id', 'desc')
            ->limit($this->maxAllowedQuotesCount)->get();
        $this->quotes = $quotes->toArray();
    }

    protected function calculateCountOfQuotesToFetchFromSimpsonsApi(): int
    {
        $difference = $this->maxAllowedQuotesCount - count($this->quotes);

        if ($difference > 0) {
            // Fetch as many as needed to reach maximum allowed count
            return $difference;
        } else {
            // Otherwise fetch exactly one new quote
            return 1;
        }
    }

    protected function fetchQuotesFromSimpsonsApi(int $countOfQuotesToFetch): array
    {
        $jsonString = $this->simpsonsQuotesFetcher::fetch($countOfQuotesToFetch);
        return $this->jsonStringToModels($jsonString);
    }

    protected function jsonStringToModels(string $json): array
    {
        $quotes = json_decode($json, true);
        $this->validateDecodedJsonQuotes($quotes);

        $models = [];
        foreach ($quotes as $quote) {
            $models[] = $this->simpsonsQuoteModel::create($quote);
        }

        return $models;
    }

    protected function validateDecodedJsonQuotes(mixed $quotes): void
    {
        if (!is_array($quotes)) {
            throw new Exception('Could not decode json string');
        }

        foreach ($quotes as $quote) {
            if (array_keys($quote) != $this->expectedFields) {
                throw new Exception('Simpsons quote to parse has unexpected fields');
            }

            foreach ($this->expectedFields as $field) {
                if (empty($quote[$field]) || !is_string($quote[$field])) {
                    throw new Exception('Simpsons quote to parse has invalid field value');
                }
            }
        }
    }

    protected function addQuotesToStorage(SimpsonsQuoteModel ...$quotes): void
    {
        foreach ($quotes as $quote) {
            $quote->save();
            // Prepend new quote to continue ordering by ID desc
            array_unshift($this->quotes, $quote);
        }
    }

    protected function pruneObsoleteQuotes(): void
    {
        // Keep only the newest quotes up to allowed maximum in database and $this->quotes

        $this->dbFacade::table('simpsons_quotes')
            ->whereRaw('`id` <
                (
                select min(`id`)
                    from (
                        select `id`
                        from `simpsons_quotes`
                        order by `id` desc
                        limit ?
                    ) as tmp
                )',
                [$this->maxAllowedQuotesCount]
            )
            ->delete();

        // Ordered by ID desc
        $this->quotes = array_slice($this->quotes, 0, $this->maxAllowedQuotesCount);
    }

}
