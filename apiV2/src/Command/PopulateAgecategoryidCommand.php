<?php
namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\ORM\TableRegistry;


class PopulateAgecategoryidCommand extends Command
{
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $this->loadModel('Agecategories');
        $this->loadModel('Categorycodes');

        $categorycodes = $this->Categorycodes->find()->toArray();


        $count = 0;

        foreach ($categorycodes as $category) {
            if (empty($category->agecategory_id)) {

                //aggiorno athletes
                $agecategory = $this->Agecategories->find()
                ->where(['description' => $category->categoria])
                ->first();

                $category->agecategory_id = $agecategory->id;
                $this->Categorycodes->save($category);

                $count++;
            }
        }

        $io->out("Updated $count records with new NanoIDs.");
    }
}
