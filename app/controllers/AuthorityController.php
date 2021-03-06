<?php

/**
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * PHP Version 5.5
 */

/**
 * Class for signin, signup with phone or E-mail, send SMS and E-mail and recovery password.
 *
 * Send SMS service use Yuntongxun http://www.yuntongxun.com, and domain mail use Netease free service.
 * This two work in the beanstalk queue service, queue server monitoring visit http://beanstalk.pinai521.com
 *
 * @uses        Laravel The PHP frameworks for web artisans http://laravel.com
 * @author      Ri Xu http://xuri.me <xuri.me@gmail.com>
 * @copyright   Copyright (c) Harbin Wizard Techonlogy Co., Ltd.
 * @link        http://www.jinglingkj.com
 * @license     Licensed under The MIT License http://www.opensource.org/licenses/mit-license.php
 * @version     Release: 0.1 2014-12-25
 */

class AuthorityController extends BaseController
{
    /**
     * function getSignout
     * @return Response
     */
    public function getSignout()
    {
        Auth::logout();
        return Redirect::to('/');
    }

    /**
     * View: Signin
     * @return Response
     */
    public function getSignin()
    {
        return View::make('authority.signin');
    }

    /**
     * View: postCaptcha
     * @return Response
     */
    public function postCaptcha()
    {
        return Response::json(
            array(
                'success'   => true,
                'captcha'   => HTML::image(URL::to('simplecaptcha' . '?' . time()), 'Captcha', array('class' => 'captcha_img'))
            )
        );
    }

    /**
     * Action: Signin
     * @return Response
     */
    public function postSignin()
    {
        // Credentials
        $credentials = array(
            'email'     => Input::get('username'),
            'password'  => md5(Input::get('password')
        ));
        $phone_credentials = array(
            'phone'     => Input::get('username'),
            'password'  => md5(Input::get('password')
        ));
        $wap_credentials = array(
            'w_id'      => Input::get('username'),
            'password'  => md5(Input::get('password')
        ));
        // Remember login status
        $remember    = Input::get('remember-me', 1);
        // Verify signin
        if (Auth::attempt($credentials) || Auth::attempt($phone_credentials) || Auth::attempt($wap_credentials)) {

            // Update receiver_updated_at in like table
            DB::table('like')->where('receiver_id', Auth::user()->id)->update(array('receiver_updated_at' => Carbon::now()));

            // Signin success, redirect to the previous page that was blocked
            return Response::json(
                array(
                    'success'   => true,
                    'attempt'   => URL::previous()
                )
            );
        } else {
            // Signin fail, redirect back
            return Response::json(
                array(
                    'success'   => false,
                    'attempt'   => Lang::get('authority.signin_error')
                )
            );
        }
    }

    /**
     * Action: PostVerifyCode
     * @return Json Ajax Response
     */
    public function postVerifyCode()
    {
        // Send Recovery Password SMS
        if (Input::get('forgot_password')) {
            $phone       = array (
                'phone' => Input::get('phone')
            );
            // Create validation rules
            $rules = array(
                'phone'          => 'required|digits:11|exists:users'
            );
            // Custom validation message
            $messages = array(
                'phone.required' => Lang::get('authority.phone_required'),
                'phone.digits'   => Lang::get('authority.phone_digits'),
                'phone.exists'   => Lang::get('authority.phone_exists')
            );
            // Begin verification
            $validator = Validator::make($phone, $rules, $messages);
            if ($validator->passes()) {
                $verify_code = rand(100000,999999);
                Session::forget('verify_code');
                Session::put('verify_code', $verify_code);

                Queue::push('SendSMSQueue', [
                    'phone'         => Input::get('phone'),
                    'verify_code'   => $verify_code
                ]);

                return Response::json(
                    array(
                        'success'       => true,
                        'success_info'  => Lang::get('authority.send_success')
                    )
                );
            } else {
                return Response::json(
                    array(
                        'fail'      => true,
                        'errors'    => $validator->getMessageBag()->toArray()
                    )
                );
            }
        } else {
            if (SimpleCaptcha::check(Input::get('captcha')) == true) {

                $phone       = array (
                    'phone' => Input::get('phone')
                );
                // Create validation rules
                $rules = array(
                    'phone'          => 'required|digits:11|unique:users'
                );
                // Custom validation message
                $messages = array(
                    'phone.required' => Lang::get('authority.phone_required'),
                    'phone.digits'   => Lang::get('authority.phone_digits'),
                    'phone.unique'   => Lang::get('authority.phone_exists')
                );
                // Begin verification
                $validator = Validator::make($phone, $rules, $messages);
                if ($validator->passes()) {
                    $verify_code = rand(100000,999999);
                    Session::forget('verify_code');
                    Session::put('verify_code', $verify_code);

                    Queue::push('SendSMSQueue', [
                        'phone'         => Input::get('phone'),
                        'verify_code'   => $verify_code
                    ]);

                    return Response::json(
                        array(
                            'success'       => true,
                            'success_info'  => Lang::get('authority.send_success')
                        )
                    );
                } else {
                    return Response::json(
                        array(
                            'fail'      => true,
                            'errors'    => $validator->getMessageBag()->toArray()
                        )
                    );
                }
            } else {
                return Response::json(
                    array(
                        'fail'          => true,
                        'captcha_error' => Lang::get('authority.captcha_error')
                    )
                );
            }
        }
    }

    /**
     * Action: postSMSReset
     * @return Json Ajax Response
     */
    public function postSMSReset()
    {
        // Get all form data.
        $data = Input::all();

        // Create validation rules
        $rules = array(
            'phone'                 => 'required|digits:11|exists:users',
            'password'              => 'required|between:6,16|confirmed',
            'password_confirmation' => 'required',
            'sms_code'              => 'required|digits:6'
        );

        // Custom validation message
        $messages = array(
            'phone.required'                    => Lang::get('authority.phone_required'),
            'phone.digits'                      => Lang::get('authority.phone_digits'),
            'phone.exists'                      => Lang::get('authority.phone_exists'),
            'password.required'                 => Lang::get('authority.password_required'),
            'password.between'                  => '密码长度请保持在:min到:max位之间。',
            'password.confirmed'                => Lang::get('authority.password_confirmed'),
            'password_confirmation.required'    => Lang::get('authority.password_confirmed_required'),
            'sms_code.required'                 => Lang::get('authority.sms_code_required'),
            'sms_code.digits'                   => Lang::get('authority.sms_code_digits')
        );

        // Begin verification
        $validator      = Validator::make($data, $rules, $messages);
        $phone          = Input::get('phone');
        $verify_code    = Session::get('verify_code');
        $sms_code       = Input::get('sms_code');
        if ($validator->passes() && $sms_code == $verify_code) {

            // Verification success, add user
            $user           = User::where('phone', $phone)->first();
            $user->password = Hash::make(md5(Input::get('password')));
            if ($user->save()) {

                // Update users password in easemob system
                $easemob    = getEasemob();

                // New request or new Json request returns a request object
                $regChat    = cURL::newJsonRequest('put', 'https://a1.easemob.com/jinglingkj/pinai/users/' . $user->id . '/password', ['newpassword' => $user->password])
                    ->setHeader('content-type', 'application/json')
                    ->setHeader('Accept', 'json')
                    ->setHeader('Authorization', 'Bearer '.$easemob->token)
                    ->setOptions([CURLOPT_VERBOSE => true])
                    ->send();

                // Redirect to Home Page
                Auth::login($user);
                // Add user fail
                return Response::json(
                    array(
                        'success'       => true
                    )
                );
            } else {
                // Add user fail
                return Response::json(
                    array(
                        'fail'      => true,
                        'errors'    => Lang::get('authority.reset_password_error')
                    )
                );
            }
        } else {
            // Add user fail
            return Response::json(
                array(
                    'fail'      => true,
                    'errors'    => $validator->getMessageBag()->toArray()
                )
            );
        }
    }

    /**
     * View: Signup
     * @return Response
     */
    public function getSignup()
    {
        return View::make('authority.signup');
    }

    /**
     * Action: Signup
     * @return Response
     */
    public function postSignup()
    {

        if(Input::get('type') === 'email') {

            // Get all form data.
            $data = Input::all();

            // Create validation rules
            $rules = array(
                'email'     => 'required|email|unique:users',
                'password'  => 'required|alpha_dash|between:6,16|confirmed',
                'sex'       => 'required',
            );

            // Custom validation message
            $messages = array(
                'email.required'        => Lang::get('authority.email_required'),
                'email.email'           => Lang::get('authority.email_email'),
                'email.unique'          => Lang::get('authority.email_unique'),
                'password.required'     => Lang::get('authority.password_required'),
                'password.alpha_dash'   => Lang::get('authority.password_alpha_dash'),
                'password.between'      => '密码长度请保持在:min到:max位之间。',
                'password.confirmed'    => Lang::get('authority.password_confirmed'),
                'sex.required'          => Lang::get('authority.sex_required')
            );

            // Begin verification
            $validator = Validator::make($data, $rules, $messages);
            if ($validator->passes()) {

                // Verification success，add user
                $user           = new User;
                $user->email    = Input::get('email');
                $user->sex      = Input::get('sex');
                $user->password = md5(Input::get('password'));

                if ($user->save()) {
                    $profile            = new Profile;
                    $profile->user_id   = $user->id;
                    $profile->save();

                    // Add user success
                    // Generate activation code
                    $activation         = new Activation;
                    $activation->email  = $user->email;
                    $activation->sex    = $user->sex;
                    $activation->token  = str_random(40);
                    $activation->save();

                    // Chat Register
                    Queue::push('AddUserQueue', [
                                        'username'  => $user->id,
                                        'password'  => $user->password,
                                    ]);

                    // Create floder to store chat record
                    // File::makeDirectory(app_path('chatrecord/user_' . $user->id, 0777, true));

                    // Send activation mail
                    $with = array('activationCode' => $activation->token);
                    Mail::later(10, 'emails.auth.activation', $with, function ($message) use ($user) {
                        $message
                            ->to($user->email)
                            ->subject('聘爱 账号激活邮件'); // Subject
                    });

                    // Redirect to a registration page, prompts user to activate
                    return Response::json(
                        array(
                            'success'   => true,
                            'attempt'   => route('signupSuccess', $user->email)
                        )
                    );
                } else {
                    // Add user fail
                    return Response::json(
                        array(
                            'success'   => false,
                            'attempt'   => '注册失败。'
                        )
                    );
                }
            } else {
                // Verification fail, redirect back
                return Response::json(
                    array(
                        'success'       => false,
                        'error_info'    => $validator->messages()->toArray()
                    )
                );
            }
        } else {
            if (SimpleCaptcha::check(Input::get('captcha')) == true) {

                // Get all form data.
                $data = Input::all();

                // Create validation rules
                $rules = array(
                    'phone'     => 'required|digits:11|unique:users',
                    'password'  => 'required|alpha_dash|between:6,16|confirmed',
                    'sms_code'  => 'required|digits:6',
                    'sex'       => 'required',
                );

                // Custom validation message
                $messages = array(
                    'phone.required'        => Lang::get('authority.phone_required'),
                    'phone.digits'          => Lang::get('authority.phone_digits'),
                    'phone.unique'          => Lang::get('authority.phone_unique'),
                    'password.required'     => Lang::get('authority.password_required'),
                    'password.alpha_dash'   => Lang::get('authority.password_alpha_dash'),
                    'password.between'      => '密码长度请保持在:min到:max位之间。',
                    'password.confirmed'    => Lang::get('authority.password_confirmed'),
                    'sms_code.required'     => Lang::get('authority.sms_code_required'),
                    'sms_code.digits'       => Lang::get('authority.sms_code_digits'),
                    'sex.required'          => Lang::get('authority.sex_required'),
                );

                // Begin verification
                $validator   = Validator::make($data, $rules, $messages);
                $phone       = Input::get('phone');
                $verify_code = Session::get('verify_code');
                $sms_code    = Input::get('sms_code');
                if ($validator->passes() && $sms_code == $verify_code) {

                    // Verification success, add user
                    $user               = new User;
                    $user->phone        = $phone;
                    $user->password     = md5(Input::get('password'));
                    $user->sex          = Input::get('sex');
                    $user->activated_at = date('Y-m-d H:m:s');

                    if ($user->save()) {
                        $profile            = new Profile;
                        $profile->user_id   = $user->id;
                        $profile->save();

                        Queue::push('AddUserQueue', [
                                        'username'  => $user->id,
                                        'password'  => $user->password,
                                    ]);

                        // Create floder to store chat record
                        // File::makeDirectory(app_path('chatrecord/user_' . $user->id, 0777, true));

                        // User signin
                        Auth::login($user);

                        // Redirect to a registration page, prompts user to activate
                        return Response::json(
                            array(
                                'success'   => true,
                                'attempt'   => route('account')
                            )
                        );
                    } else {
                        // Add user fail
                        return Response::json(
                            array(
                                'success'   => false,
                                'attempt'   => Lang::get('authority.signup_error')
                            )
                        );
                    }
                } else {
                    // Add user fail
                    return Response::json(
                        array(
                            'success'       => false,
                            'error_info'    => $validator->messages()->toArray()
                        )
                    );
                }
            } else {
                return Response::json(
                    array(
                        'success'   => false,
                        'attempt'   => Lang::get('authority.captcha_error')
                    )
                );
            }
        }
    }

    /**
     * View: Signuo success, prompts user to activate
     * @param  string $email user E-mail
     * @return Response
     */
    public function getSignupSuccess($email)
    {
        // Confirmed the existence of this inactive mailboxes
        $activation = Activation::whereRaw("email = '{$email}'")->first();
        // No mailboxes in the database, throw 404
        is_null($activation) AND App::abort(404);
        // Prompts user to activate
        return View::make('authority.signupSuccess')->with('email', $email);
    }

    /**
     * Action: Activate account
     * @param  string $activationCode Activation tokens
     * @return Response
     */
    public function getActivate($activationCode)
    {
        // Database authentication tokens
        $activation = Activation::where('token', $activationCode)->first();

        // No tokens in the database, throw 404
        is_null($activation) AND App::abort(404);

        // Database tokens
        // Activate the corresponding user
        $user               = User::where('email', $activation->email)->first();
        $user->activated_at = new Carbon;
        $user->sex          = $activation->sex;
        $user->save();

        // Delete tokens
        $activation->delete();

        // Activation success
        // Log a user into the application by ID
        Auth::loginUsingId($user->id);
        return View::make('authority.activationSuccess');
    }

    /**
     * Page: Forgot password, send a password reset mail
     * @return Response
     */
    public function getForgotPassword()
    {
        return View::make('authority.password.remind');
    }

    /**
     * Action: Forgot password, send a password reset mail
     * @return Response
     */
    public function postForgotPassword()
    {
        // Calling the system-provided class
        $response = Password::remind(Input::only('email'), function ($m, $user, $token) {
            $m->subject('聘爱 密码重置邮件'); // Title
        });
        // Detect mail and send a password reset message
        switch ($response) {
            case Password::INVALID_USER:
                return Redirect::back()->with('error', Lang::get($response));
            case Password::REMINDER_SENT:
                return Redirect::back()->with('status', Lang::get($response));
        }
    }

    /**
     * View: Reset password
     * @param   varchar     $token
     * @return  Response
     */
    public function getReset($token)
    {
        // No tokens in the database, throw 404
        is_null(PassowrdReminder::where('token', $token)->first()) AND App::abort(404);
        return View::make('authority.password.reset')->with('token', $token);
    }

    /**
     * Action: Reset password
     * @return Response
     */
    public function postReset()
    {
        // Invoke system comes with the password reset process
        $credentials = Input::only(
            'email', 'password', 'password_confirmation', 'token'
        );
        $response = Password::reset($credentials, function ($user, $password) {
            // Save new password
            $user->password = md5($password);
            $user->save();

            // Update users password in easemob system
            $easemob    = getEasemob();

            // New request or new Json request returns a request object
            $regChat            = cURL::newJsonRequest('put', 'https://a1.easemob.com/jinglingkj/pinai/users/' . $user->id . '/password', ['newpassword' => $user->password])
                ->setHeader('content-type', 'application/json')
                ->setHeader('Accept', 'json')
                ->setHeader('Authorization', 'Bearer '.$easemob->token)
                ->setOptions([CURLOPT_VERBOSE => true])
                ->send();

            // User signin
            Auth::login($user);
        });
        switch ($response) {
            case Password::INVALID_PASSWORD:
                // no break
            case Password::INVALID_TOKEN:
                // no break
            case Password::INVALID_USER:
                return Redirect::back()->with('error', Lang::get($response));
            case Password::PASSWORD_RESET:
                return Redirect::to('/');
        }
    }

}
