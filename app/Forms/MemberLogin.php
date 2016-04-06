<?php namespace App\Forms;

use Kris\LaravelFormBuilder\Form;

class MemberLogin extends Form
{
    public function buildForm()
    {
        $this
            ->add('email', 'email')
            ->add('password', 'password')
            ->add('login', 'submit');
    }
}