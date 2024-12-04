<?php
namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\ORM\TableRegistry;
use Hidehalo\Nanoid\Client;

class UpdateCompetitionidCommand extends Command
{
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $this->loadModel('Competitions');
        $this->loadModel('AthleteInscriptions');
        $this->loadModel('ClubInscriptions');
        $this->loadModel('Teams');
        $competitions = $this->Competitions->find()->toArray();
        $client = new Client();

        $count = 0;

        foreach ($competitions as $competition) {

                //aggiorno athletes
                $athleteInscriptions = $this->AthleteInscriptions->find()
                ->where(['competition_id' => $competition->id])
                ->toArray();

                foreach ($athleteInscriptions as $athleteInscription){
                    $athleteInscription->competition_code = $competition->code;
                    $this->AthleteInscriptions->save($athleteInscription);
                }

                //aggiorno ClubInscriptions
                $clubInscriptions = $this->ClubInscriptions->find()
                ->where(['competition_id' => $competition->id])
                ->toArray();

                foreach ($clubInscriptions as $clubInscription){
                    $clubInscription->competition_code = $competition->code;
                    $this->ClubInscriptions->save($clubInscription);
                }

                //aggiorno Teams
                $teams = $this->Teams->find()
                ->where(['competition_id' => $competition->id])
                ->toArray();

                foreach ($teams as $team){
                    $team->competition_code = $competition->code;
                    $this->Teams->save($team);
                }

                $count++;
        }

        $io->out("Updated $count records with new NanoIDs.");
    }
}
