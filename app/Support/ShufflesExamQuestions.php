<?php

namespace App\Support;

trait ShufflesExamQuestions
{
    protected function buildShuffleMap($questions): array
    {
        $questionIds = $questions->pluck('id')->shuffle()->values()->all();
        $answerOrders = [];

        foreach ($questions as $question) {
            $answerOrders[(string) $question->id] = $question->answers
                ->pluck('id')
                ->shuffle()
                ->values()
                ->all();
        }

        return [
            'questions' => $questionIds,
            'answers' => $answerOrders,
        ];
    }

    protected function applyShuffleMap($questions, ?array $shuffleMap)
    {
        if (empty($shuffleMap['questions']) || !is_array($shuffleMap['questions'])) {
            return $questions->values();
        }

        $byId = $questions->keyBy('id');
        $ordered = collect();

        foreach ($shuffleMap['questions'] as $questionId) {
            $question = $byId->get($questionId);
            if (!$question) {
                continue;
            }

            $answerOrder = $shuffleMap['answers'][(string) $questionId]
                ?? $shuffleMap['answers'][$questionId]
                ?? null;

            if (is_array($answerOrder) && !empty($answerOrder)) {
                $answersById = $question->answers->keyBy('id');
                $shuffledAnswers = collect();
                foreach ($answerOrder as $answerId) {
                    if ($answersById->has($answerId)) {
                        $shuffledAnswers->push($answersById->get($answerId));
                    }
                }
                foreach ($question->answers as $answer) {
                    if (!$shuffledAnswers->contains('id', $answer->id)) {
                        $shuffledAnswers->push($answer);
                    }
                }
                $question->setRelation('answers', $shuffledAnswers->values());
            } else {
                $question->setRelation('answers', $question->answers->shuffle()->values());
            }

            $ordered->push($question);
        }

        foreach ($questions as $question) {
            if (!$ordered->contains('id', $question->id)) {
                $question->setRelation('answers', $question->answers->shuffle()->values());
                $ordered->push($question);
            }
        }

        return $ordered->values();
    }
}
