<?php

namespace App\Services\Checklist;

class DeleteChecklistService
{
    /**
     * @param $checklist
     * @return mixed
     */
    public function run($checklist)
    {
        if ($checklist->products()->count() == 0) {
            $checklist->delete();
            session()->flash('alert-success', 'Checklist excluído com sucesso!');
            return response(route('configurations.checklists.index'));
        }
        session()->flash('alert-warning', 'Não foi possível excluir, registros vinculados!');
        return false;
    }
}
