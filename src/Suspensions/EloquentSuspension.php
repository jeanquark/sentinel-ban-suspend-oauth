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

use Illuminate\Database\Eloquent\Model;

class EloquentSuspension extends Model implements SuspensionInterface
{
    /**
     * {@inheritDoc}
     */
    protected $table = 'throttle';

    /**
     * Suspensions time in minutes.
     *
     * @var int
     */
    protected static $suspensionTime = 15;

    /**
     * {@inheritDoc}
     */
    protected $fillable = [
        'user_id',
        'suspended',
        'banned',
        'suspended_at',
        'banned_at'
    ];

    /**
     * Get mutator for the "suspended" attribute.
     *
     * @param  mixed  $suspended
     * @return bool
     */
    public function getSuspendedAttribute($suspended)
    {
        return (bool) $suspended;
    }

    /**
     * Set mutator for the "suspended" attribute.
     *
     * @param  mixed  $suspended
     * @return void
     */
    public function setSuspendedAttribute($suspended)
    {
        $this->attributes['suspended'] = (bool) $suspended;
    }

    /**
     * {@inheritDoc}
     */
    /*public function getCode()
    {
        return $this->attributes['suspended'];
    }*/
}
