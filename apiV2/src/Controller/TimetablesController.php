<?php
declare(strict_types=1);

namespace App\Controller;
use RestApi\Controller\ApiController;

/**
 * Timetables Controller
 *
 * @property \App\Model\Table\TimetablesTable $Timetables
 * @method \App\Model\Entity\Timetable[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TimetablesController extends ApiController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Competitions'],
        ];
        $timetables = $this->paginate($this->Timetables);

        $this->set(compact('timetables'));
    }

    /**
     * View method
     *
     * @param string|null $id Timetable id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $timetable = $this->Timetables->get($id, [
            'contain' => ['Competitions'],
        ]);

        $this->set(compact('timetable'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $timetable = $this->Timetables->newEmptyEntity();
        if ($this->request->is('post')) {
            $timetable = $this->Timetables->patchEntity($timetable, $this->request->getData());
            if ($this->Timetables->save($timetable)) {
                $this->Flash->success(__('The timetable has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The timetable could not be saved. Please, try again.'));
        }
        $competitions = $this->Timetables->Competitions->find('list', ['limit' => 200])->all();
        $this->set(compact('timetable', 'competitions'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Timetable id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $timetable = $this->Timetables->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $timetable = $this->Timetables->patchEntity($timetable, $this->request->getData());
            if ($this->Timetables->save($timetable)) {
                $this->Flash->success(__('The timetable has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The timetable could not be saved. Please, try again.'));
        }
        $competitions = $this->Timetables->Competitions->find('list', ['limit' => 200])->all();
        $this->set(compact('timetable', 'competitions'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Timetable id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $timetable = $this->Timetables->get($id);
        if ($this->Timetables->delete($timetable)) {
            $this->Flash->success(__('The timetable has been deleted.'));
        } else {
            $this->Flash->error(__('The timetable could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }


    public function addTimetable(){
    

        $timetables = [];

        $recordU6 = array(
            'competition_id' => 'SCCSC-2026',
            'category' => 'UNDER 6',
            'specialita' => 'COMBINATA',
            'time' => '9.00',
        );

        $timetables[] = $this->Timetables->patchEntity($this->Timetables->newEmptyEntity(), $recordU6);

        $recordU8 = array(
            'competition_id' => 'SCCSC-2026',
            'category' => 'UNDER 8',
            'specialita' => 'COMBINATA',
            'time' => '9.00',
        );

        $timetables[] = $this->Timetables->patchEntity($this->Timetables->newEmptyEntity(), $recordU8);

        $recordU10 = array(
            'competition_id' => 'SCCSC-2026',
            'category' => 'UNDER 10',
            'specialita' => 'COMBINATA',
            'time' => '9.00',
        );

        $timetables[] = $this->Timetables->patchEntity($this->Timetables->newEmptyEntity(), $recordU10);

        $recordU12 = array(
            'competition_id' => 'SCCSC-2026',
            'category' => 'UNDER 12',
            'specialita' => 'COMBINATA',
            'time' => '10.00',
        );

        $timetables[] = $this->Timetables->patchEntity($this->Timetables->newEmptyEntity(), $recordU12);

        $recordU12Kumite = array(
            'competition_id' => 'SCCSC-2026',
            'category' => 'UNDER 12',
            'specialita' => 'KATA E KUMITE',
            'time' => '10.00',
        );

        $timetables[] = $this->Timetables->patchEntity($this->Timetables->newEmptyEntity(), $recordU12Kumite);

        $recordESO = array(
            'competition_id' => 'SCCSC-2026',
            'category' => 'ESORDINETI',
            'specialita' => 'KATA',
            'time' => '11.30',
        );

        $timetables[] = $this->Timetables->patchEntity($this->Timetables->newEmptyEntity(), $recordESO);

        $recordESOK = array(
            'competition_id' => 'SCCSC-2026',
            'category' => 'ESORDINETI',
            'specialita' => 'KUMITE',
            'time' => '12.30',
        );

        $timetables[] = $this->Timetables->patchEntity($this->Timetables->newEmptyEntity(), $recordESOK);

        $recordCAD = array(
            'competition_id' => 'SCCSC-2026',
            'category' => 'CADETTI',
            'specialita' => 'KATA',
            'time' => '13.30',
        );

        $timetables[] = $this->Timetables->patchEntity($this->Timetables->newEmptyEntity(), $recordCAD);

        $recordCADK = array(
            'competition_id' => 'SCCSC-2026',
            'category' => 'CADETTI',
            'specialita' => 'KUMITE',
            'time' => '14.30',
        );

        $timetables[] = $this->Timetables->patchEntity($this->Timetables->newEmptyEntity(), $recordCADK);

        $recordSQ1 = array(
            'competition_id' => 'SCCSC-2026',
            'category' => 'ESO/CAD/JUN',
            'specialita' => 'KATA SQUADRE',
            'time' => '15.00',
        );

        $timetables[] = $this->Timetables->patchEntity($this->Timetables->newEmptyEntity(), $recordSQ1);

        $recordJUN = array(
            'competition_id' => 'SCCSC-2026',
            'category' => 'JUNIOR',
            'specialita' => 'KATA',
            'time' => '15.00',
        );

        $timetables[] = $this->Timetables->patchEntity($this->Timetables->newEmptyEntity(), $recordJUN);

        $recordJUNK = array(
            'competition_id' => 'SCCSC-2026',
            'category' => 'JUNIOR',
            'specialita' => 'KUMITE',
            'time' => '15.30',
        );

        $timetables[] = $this->Timetables->patchEntity($this->Timetables->newEmptyEntity(), $recordJUNK);

        $recordSEN = array(
            'competition_id' => 'SCCSC-2026',
            'category' => 'SENIOR',
            'specialita' => 'KATA',
            'time' => '17.00',
        );

        $timetables[] = $this->Timetables->patchEntity($this->Timetables->newEmptyEntity(), $recordSEN);

        $recordSENK = array(
            'competition_id' => 'SCCSC-2026',
            'category' => 'SENIOR',
            'specialita' => 'KUMITE',
            'time' => '17.30',
        );

        $timetables[] = $this->Timetables->patchEntity($this->Timetables->newEmptyEntity(), $recordSENK);

        $recordSQ2 = array(
            'competition_id' => 'SCCSC-2026',
            'category' => 'SENIOR/MASTER',
            'specialita' => 'KATA SQUADRE',
            'time' => '17.30',
        );

        $timetables[] = $this->Timetables->patchEntity($this->Timetables->newEmptyEntity(), $recordSQ2);

        $recordMAS = array(
            'competition_id' => 'SCCSC-2026',
            'category' => 'MASTER',
            'specialita' => 'KATA',
            'time' => '17.30',
        );

        $timetables[] = $this->Timetables->patchEntity($this->Timetables->newEmptyEntity(), $recordMAS);

        $this->Timetables->saveMany($timetables);

    }
}
