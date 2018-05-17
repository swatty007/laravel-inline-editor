<?php

namespace swatty007\LaravelInlineEditor\Http;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class InlineController extends BaseController
{
    public function index(Request $request)
    {
        $blocks = $request->blocks;

        foreach ($blocks as $block)
        {
            Validator::make($block, config('laravel-inline-editor.rules') )->validate();

            DB::table($block['table'])
                ->where($block['source_key'], $block['source_value'])
                ->update([ $block['target_key'] => $block['target_value']]);
        }

        return 'ok';
    }
}