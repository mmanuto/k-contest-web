<?php
namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\ORM\TableRegistry;
use Hidehalo\Nanoid\Client;

class PopulateNanoidCommand extends Command
{
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $this->loadModel('Clubs');
        $this->loadModel('Athletes');
        $this->loadModel('ClubInscriptions');
        $this->loadModel('Teams');
        $clubs = $this->Clubs->find()->toArray();
        $client = new Client();

        $count = 0;

        foreach ($clubs as $club) {
            if (empty($club->nanoid)) {
                $club->nanoid = $client->generateId();
                $this->Clubs->save($club);

                //aggiorno athletes
                $athletes = $this->Athletes->find()
                ->where(['club_id' => $club->id])
                ->toArray();

                foreach ($athletes as $athlete){
                    $athlete->club_nanoid = $club->nanoid;
                    $this->Athletes->save($athlete);
                }

                //aggiorno ClubInscriptions
                $clubInscriptions = $this->ClubInscriptions->find()
                ->where(['club_id' => $club->id])
                ->toArray();

                foreach ($clubInscriptions as $clubInscription){
                    $clubInscription->club_nanoid = $club->nanoid;
                    $this->ClubInscriptions->save($clubInscription);
                }

                //aggiorno Teams
                $teams = $this->Teams->find()
                ->where(['club_id' => $club->id])
                ->toArray();

                foreach ($teams as $team){
                    $team->club_nanoid = $club->nanoid;
                    $this->Teams->save($team);
                }

                $count++;
            }
        }

        $io->out("Updated $count records with new NanoIDs.");
    }
}
