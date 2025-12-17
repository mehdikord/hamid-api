<?php
namespace App\Interfaces\Projects;
interface UserProjectInterface
{

    public function all();

    public function reports();

    public function invoices();
    public function fields($project);


}
