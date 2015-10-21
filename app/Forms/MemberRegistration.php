<?php namespace App\Forms;

use Kris\LaravelFormBuilder\Form;

class MemberRegistration extends Form
{
    public function buildForm()
    {
        $this
            ->add('name', 'text')
            ->add('email', 'email')    
            ->add('password', 'password')
            ->add('password_confirmation', 'password', ['label' => 'Type your password again'])
            ->add('first_name', 'text', ['label' => 'First Name'])
            ->add('last_name', 'text', ['label' => 'Last Name'])
            ->add('organization', 'text', ['label' => 'Organization'])    
            ->add('save', 'submit', ['label' => 'Register'])
            ->add('clear', 'reset', ['label' => 'Reset']);
    }
}