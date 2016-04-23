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

namespace Cartalyst\Sentinel\Suspensions;

use Cartalyst\Sentinel\Users\UserInterface;

interface SuspensionRepositoryInterface
{
    /**
     * Create a new activation record and code.
     *
     * @param  \Cartalyst\Sentinel\Users\UserInterface  $user
     * @return \Cartalyst\Sentinel\Activations\ActivationInterface
     */
    public function create(UserInterface $user);

    /**
     * Checks if a valid activation for the given user exists.
     *
     * @param  \Cartalyst\Sentinel\Users\UserInterface  $user
     * @param  string  $code
     * @return \Cartalyst\Sentinel\Activations\ActivationInterface|bool
     */
    //public function exists(UserInterface $user, $code = null);
    public function exists(UserInterface $user);

    /**
     * Completes the activation for the given user.
     *
     * @param  \Cartalyst\Sentinel\Users\UserInterface  $user
     * @param  string  $code
     * @return bool
     */
    //public function complete(UserInterface $user, $code);
    public function suspend(UserInterface $user);

    /**
     * Checks if a valid activation has been completed.
     *
     * @param  \Cartalyst\Sentinel\Users\UserInterface  $user
     * @return \Cartalyst\Sentinel\Activations\ActivationInterface|bool
     */
    public function isSuspended(UserInterface $user);

    /**
     * Remove an existing activation (deactivate).
     *
     * @param  \Cartalyst\Sentinel\Users\UserInterface  $user
     * @return bool|null
     */
    public function remove(UserInterface $user);

    /**
     * Remove expired activation codes.
     *
     * @return int
     */
    public function removeExpired();
}
