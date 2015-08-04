<?php namespace App\Forms;

use Kris\LaravelFormBuilder\Form;

class MemberRegistration extends Form
{
    public function buildForm()
    {
        $this
            ->add('name', 'text')
            ->add('password', 'password')
            ->add('email', 'text');
    }
}