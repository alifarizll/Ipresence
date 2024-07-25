<?php

namespace App\Observers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class SignatureObserver
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function creating($model)
    {
        $user = Auth::guard('custom')->user();
        if ($user) {
            $model->created_by = $user->sub;
            $model->updated_by = $user->sub;
        }
        $model->created_ip = $this->request->ip();
        $model->updated_ip = $this->request->ip();
    }

    public function updating($model)
    {
        $user = Auth::guard('custom')->user();
        if ($user) {
            $model->updated_by = $user->sub;
        }
        $model->updated_ip = $this->request->ip();
    }

    public function deleting($model)
    {
        $user = Auth::guard('custom')->user();
        if ($user) {
            $model->deleted_by = $user->sub;
        }
        $model->deleted_ip = $this->request->ip();
        $model->save();
    }
}
