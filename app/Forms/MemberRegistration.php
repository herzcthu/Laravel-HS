<?php namespace App\Forms;

use Kris\LaravelFormBuilder\Form;

class MemberRegistration extends Form
{
    
    public function buildForm()
    {
        $organization = \App\Organization::lists('short','id')->toArray();
        $this
            ->add('name', 'text')
            ->add('email', 'email')    
            ->add('password', 'password')
            ->add('password_confirmation', 'password', ['label' => 'Type your password again'])
            ->add('first_name', 'text', ['label' => 'First Name'])
            ->add('last_name', 'text', ['label' => 'Last Name'])
            ->add('organization', 'select', [
                                    'choices' => $organization,
                                    'empty_value' => '=== Select Organization ==='], ['label' => 'Organization'])    
            ->add('save', 'submit', ['label' => 'Register'])
            ->add('clear', 'reset', ['label' => 'Reset']);
    }
}