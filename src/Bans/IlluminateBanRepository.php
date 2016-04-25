<?php

/**
 * Part of the Sentinel package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Sentinel
 * @version    2.0.8
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Cartalyst\Sentinel\Bans;

use Carbon\Carbon;
use Cartalyst\Sentinel\Users\UserInterface;
use Cartalyst\Support\Traits\RepositoryTrait;

use App\User;

class IlluminateBanRepository implements BanRepositoryInterface
{
    use RepositoryTrait;

    /**
     * The Eloquent activation model name.
     *
     * @var string
     */
    protected $model = 'Cartalyst\Sentinel\Bans\EloquentBan';

    /**
     * The activation expiration time, in seconds.
     *
     * @var int
     */
    protected $expires = 259200;

    /**
     * Create a new Illuminate activation repository.
     *
     * @param  string  $model
     * @param  int  $expires
     * @return void
     */
    public function __construct($model = null, $expires = null)
    {
        if (isset($model)) {
            $this->model = $model;
        }

        if (isset($expires)) {
            $this->expires = $expires;
        }
    }


    /**
     * Return all the registered users
     *
     * @return Collection
     */
    public function all()
    {
        //$users = $this->sentry->findAllUsers();
        $users = User::all();

        foreach ($users as $user) {
            if ($user->isActivated()) {
                $user->status = "Active";
            } else {
                $user->status = "Not Active";
            }

            //Pull Suspension & Ban info for this user
            $throttle = $this->throttleProvider->findByUserId($user->id);

            //Check for suspension
            if ($throttle->isSuspended()) {
                // User is Suspended
                $user->status = "Suspended";
            }

            //Check for ban
            if ($throttle->isBanned()) {
                // User is Banned
                $user->status = "Banned";
            }
        }

        return $users;
    }

    /**
     * {@inheritDoc}
     */
    public function create(UserInterface $user)
    {
        $ban = $this->createModel();

        //$code = $this->generateActivationCode();

        //$suspension->fill(compact('code'));

        $ban->user_id = $user->getUserId();

        $ban->save();

        return $ban;
    }

    /**
     * {@inheritDoc}
     */
    //public function exists(UserInterface $user, $code = null)
    public function exists(UserInterface $user)
    {
        //$expires = $this->expires();

        $ban = $this
            ->createModel()
            ->newQuery()
            ->where('user_id', $user->getUserId());
            //->where('suspended', false);
            //->where('created_at', '>', $expires);

        /*if ($code) {
            $suspension->where('code', $code);
        }*/
        
        return $ban->first() ?: false;
    }

    /**
     * {@inheritDoc}
     */
    //public function suspend(UserInterface $user, $code)
    public function ban(UserInterface $user)
    {
        /*$expires = $this->expires();
        $exists = $this->exists($user);
        //dd($exists);

        if ($exists) {
            $ban = $this
                ->createModel()
                ->newQuery()
                ->where('user_id', $user->getUserId())
                //->where('code', $code)
                ->where('banned', false)
                ->where('created_at', '>', $expires)
                ->first();
        }

        if ($ban === null) {
            return false;
        }

        $ban->fill([
            'banned'    => true,
            'banned_at' => Carbon::now(),
        ]);

        $ban->save();

        return true;
        */

        $exists = $this->exists($user);
        //dd($exists);
        //dd($user->getUserId());

        if ($exists) {
            $ban = $this
                ->createModel()
                ->newQuery()
                ->where('user_id', $user->getUserId())
                //->where('code', $code)
                ->where('banned', false)
                ->first();

            if ($ban === null) {
                return false;
            }

            $ban->fill([
                'banned'    => true,
                'banned_at' => Carbon::now(),
            ]);

            $ban->save();
            return true;

        } else {
            //dd($exists);
            $ban = $this
                ->createModel();

            $ban->fill([
                'user_id' => $user->getUserId(),
                'banned'    => true,
                'banned_at' => Carbon::now(),
            ]);

            $ban->save();
            return true;
        }
        
    }


    public function unban(UserInterface $user)
    {
        if ($this->isBanned($user))
        {
            $ban = $this
                ->createModel()
                ->newQuery()
                ->where('user_id', $user->getUserId())
                ->where('banned', true)
                ->first();

            if ($ban === null) {
                return false;
            }

            $ban->fill([
                'banned'    => false,
                'banned_at' => null,
            ]);

            $ban->save();
            return true;
        }
    }


    /**
     * {@inheritDoc}
     */
    /*public function banned(UserInterface $user)
    {
        $ban = $this
            ->createModel()
            ->newQuery()
            ->where('user_id', $user->getUserId())
            ->where('banned', true)
            ->first();

        return $ban ?: false;
    }*/


    /**
     * {@inheritDoc}
     */
    public function isBanned(UserInterface $user)
    {
        $ban = $this
            ->createModel()
            ->newQuery()
            ->where('user_id', $user->getUserId())
            ->where('banned', true)
            ->first();

        return $ban ?: false;
    }


    /**
     * {@inheritDoc}
     */
    public function remove(UserInterface $user)
    {
        $ban = $this->banned($user);

        if ($ban === false) {
            return false;
        }

        return $ban->delete();
    }

    /**
     * {@inheritDoc}
     */
    public function removeExpired()
    {
        $expires = $this->expires();

        return $this
            ->createModel()
            ->newQuery()
            ->where('banned', false)
            ->where('created_at', '<', $expires)
            ->delete();
    }

    /**
     * Returns the expiration date.
     *
     * @return \Carbon\Carbon
     */
    protected function expires()
    {
        return Carbon::now()->subSeconds($this->expires);
    }

    /**
     * Return a random string for an activation code.
     *
     * @return string
     */
    protected function generateSuspensionCode()
    {
        return str_random(32);
    }
}
