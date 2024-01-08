<?php

namespace App\Filters\products;
use Closure;
class QuestionsFilter
{
    public function handle($request, Closure $next){
        if(request()->filled('questions')){
            $questions_ids = collect(request('questions'))->map(fn($e)=> $e['id']);
            $questions_answers = collect(request('questions'))->map(fn($e)=> $e['answer']);
            return $next($request)
                ->whereHas('answers',function($e) use ($questions_ids,$questions_answers){
                    $e->whereIn('category_heading_questions_data_id',$questions_ids)
                      ->whereIn('ar_answer',$questions_answers);
                });
        }
        return $next($request);
    }
}
