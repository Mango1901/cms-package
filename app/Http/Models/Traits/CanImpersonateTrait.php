<?php namespace App\Http\Models\Traits;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Request;

trait CanImpersonateTrait
{
    public function setImpersonating($id)
    {
        Session::put('impersonate', $id);
        Session::put('existing_user_id', backpack_user()->id);
        backpack_auth()->loginUsingId($id);
    }

    public function stopImpersonating()
    {
        $oldUserId = Session::get("existing_user_id");
        backpack_auth()->loginUsingId($oldUserId);
        Session::forget('impersonate');
    }

    public function isImpersonating()
    {
        return Session::has('impersonate');
    }
}
