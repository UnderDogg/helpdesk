<?php

namespace App\Processors\Profile;

use Adldap\Models\User as AdldapUser;
use App\Exceptions\Profile\InvalidPasswordException;
use App\Exceptions\Profile\UnableToChangePasswordException;
use App\Http\Presenters\Profile\PasswordPresenter;
use App\Http\Requests\Profile\PasswordRequest;
use App\Jobs\User\ChangePassword;
use App\Jobs\Com\User\ChangePassword as ChangeAdPassword;
use App\Models\User;
use Illuminate\Contracts\Auth\Guard;
use App\Processors\Processor;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PasswordProcessor extends Processor
{
    /**
     * @var Guard
     */
    protected $guard;

    /**
     * @var PasswordPresenter
     */
    protected $presenter;

    /**
     * Constructor.
     *
     * @param Guard             $guard
     * @param PasswordPresenter $presenter
     */
    public function __construct(Guard $guard, PasswordPresenter $presenter)
    {
        $this->guard = $guard;
        $this->presenter = $presenter;
    }

    /**
     * Displays the form for changing the current users password.
     *
     * @return \Illuminate\View\View
     */
    public function change()
    {
        $form = $this->presenter->form();

        return view('pages.profile.show.password', compact('form'));
    }

    /**
     * Updates the current users password.
     *
     * @param PasswordRequest $request
     *
     * @throws InvalidPasswordException
     * @throws UnableToChangePasswordException
     * @throws NotFoundHttpException
     */
    public function update(PasswordRequest $request)
    {
        $credentials = ['password' => $request->input('password')];

        $user = $this->guard->user();

        // Check if we have the correct model instance.
        if ($user instanceof User) {
            $credentials['email'] = $user->email;

            // Validate the users credentials.
            if ($this->guard->validate($credentials)) {
                // We'll check if the user is from active directory
                // so we can change the password correctly if so.
                if ($user->isFromAd() && $user->adldapUser instanceof AdldapUser) {
                    $result = $this->dispatch(new ChangeAdPassword($user->adldapUser, $credentials['password']));
                } else {
                    $result = $this->dispatch(new ChangePassword($user, $credentials['password']));
                }

                if ($result !== true) {
                    throw new UnableToChangePasswordException();
                }
            } else {
                throw new InvalidPasswordException();
            }
        } else {
            throw new NotFoundHttpException();
        }
    }
}
