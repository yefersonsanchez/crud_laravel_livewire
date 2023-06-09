<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Empleado;

class Empleados extends Component
{
    use WithPagination;

	protected $paginationTheme = 'bootstrap';
    public $selected_id, $keyWord, $nombre, $correo;

    public function render()
    {
		$keyWord = '%'.$this->keyWord .'%';
        return view('livewire.empleados.view', [
            'empleados' => Empleado::latest()
						->orWhere('nombre', 'LIKE', $keyWord)
						->orWhere('correo', 'LIKE', $keyWord)
						->paginate(10),
        ]);
    }
	
    public function cancel()
    {
        $this->resetInput();
    }
	
    private function resetInput()
    {		
		$this->nombre = null;
		$this->correo = null;
    }

    public function store()
    {
        $this->validate([
		'nombre' => 'required',
		'correo' => 'required',
        ]);

        Empleado::create([ 
			'nombre' => $this-> nombre,
			'correo' => $this-> correo
        ]);
        
        $this->resetInput();
		$this->dispatchBrowserEvent('closeModal');
		session()->flash('message', 'Empleado Successfully created.');
    }

    public function edit($id)
    {
        $record = Empleado::findOrFail($id);
        $this->selected_id = $id; 
		$this->nombre = $record-> nombre;
		$this->correo = $record-> correo;
    }

    public function update()
    {
        $this->validate([
		'nombre' => 'required',
		'correo' => 'required',
        ]);

        if ($this->selected_id) {
			$record = Empleado::find($this->selected_id);
            $record->update([ 
			'nombre' => $this-> nombre,
			'correo' => $this-> correo
            ]);

            $this->resetInput();
            $this->dispatchBrowserEvent('closeModal');
			session()->flash('message', 'Empleado Successfully updated.');
        }
    }

    public function destroy($id)
    {
        if ($id) {
            Empleado::where('id', $id)->delete();
        }
    }
}