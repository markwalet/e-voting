<?php

declare(strict_types=1);

namespace App\Casts;

use App\Models\ArchivedResults;
use App\Models\PollApprovalResults;
use App\Models\PollResults;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Arr;
use JsonException;

class ArchivedResultsCast implements CastsAttributes
{
    /**
     * Cast the given value.
     * @param Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return null|ArchivedResults
     */
    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    public function get($model, string $key, $value, array $attributes): ?ArchivedResults
    {
        // Skip if empty
        if (empty($value)) {
            return null;
        }

        // Convert from JSON
        if (is_string($value)) {
            try {
                $value = \json_decode($value, true, 16, \JSON_THROW_ON_ERROR);
            } catch (JsonException $e) {
                return null;
            }
        }

        // Check keys
        if (!Arr::has($value, ['votes.start', 'votes.end', 'results', 'approval'])) {
            return null;
        }

        // Prep data
        $results = $this->getResults(Arr::get($value, 'results'));
        $approvalResults = $this->getApprovalResults(Arr::get($value, 'approval'));

        if (!$results || !$approvalResults) {
            return null;
        }

        return new ArchivedResults(
            Arr::get($value, 'votes.start'),
            Arr::get($value, 'votes.end'),
            $results,
            $approvalResults,
        );
    }

    /**
     * Converts poll results to a model
     * @param iterable $data
     * @return null|PollResults
     */
    private function getResults(iterable $data): ?PollResults
    {
        if (!Arr::has($data, ['favor', 'against', 'blank', 'votes'])) {
            return null;
        }

        return new PollResults(
            Arr::get($data, 'favor'),
            Arr::get($data, 'against'),
            Arr::get($data, 'blank'),
            Arr::get($data, 'votes'),
        );
    }

    /**
     * Converts approval results to a model
     * @param iterable $data
     * @return PollApprovalResults
     */
    private function getApprovalResults(iterable $data): PollApprovalResults
    {
        if (!Arr::has($data, ['positive', 'negative', 'neutral', 'approvals'])) {
            return null;
        }

        return new PollApprovalResults(
            Arr::get($data, 'positive'),
            Arr::get($data, 'negative'),
            Arr::get($data, 'neutral'),
            Arr::get($data, 'approvals'),
        );
    }

    /**
     * Prepare the given value for storage.
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    public function set($model, $key, $value, $attributes)
    {
        return json_encode($value);
    }
}
