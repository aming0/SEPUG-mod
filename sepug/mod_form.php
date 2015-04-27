<?php
/*
	© Universidad de Granada. Granada – 2014
	© Alejandro Molina Salazar (amolinasalazar@gmail.com). Granada – 2014
    This program is free software: you can redistribute it and/or 
    modify it under the terms of the GNU General Public License as 
    published by the Free Software Foundation, either version 3 of 
    the License.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses>.
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once ($CFG->dirroot.'/course/moodleform_mod.php');

class mod_sepug_mod_form extends moodleform_mod {

    function definition() {
        global $CFG, $DB;
		
		$update  = optional_param('update', '0', PARAM_INT);

        $mform =& $this->_form;
		
        $strrequired = get_string('required');
		
		// Comprobar que no hay una instancia SEPUG previamente creada en Moodle
		if ($DB->record_exists("sepug", array("sepuginstance"=>1)) && $update==0) {
			print_error('sepug_already_created', 'sepug');
		}
		
		// Activamos el campo "instance" que ratifica que esa entrada es una instancia
		$mform->addElement('hidden', 'sepuginstance', '1');
		$mform->setType('sepuginstance', PARAM_INT);

		//-------------------------------------------------------------------------------
		// GENERAL
        $mform->addElement('header', 'general', get_string('general', 'form'));

		// nombre
        $mform->addElement('text', 'name', get_string('name'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
		
		// descripcion
        $this->add_intro_editor(false, get_string('customintro', 'sepug'));
		
		//-------------------------------------------------------------------------------
		// DISPONIBILIDAD
        $mform->addElement('header', 'timinghdr', get_string('availability'));
		
		// fecha activacion
		$mform->addElement('date_time_selector', 'timeopen', get_string('sepugopen', 'sepug'),
            array('optional' => false));
		$mform->addHelpButton('timeopen', 'sepugopen', 'sepug');
		
		// cerrar alumnos y crear resultados
		$mform->addElement('date_time_selector', 'timeclosestudents', get_string('sepugclosestudents', 'sepug'),
            array('optional' => false));
		$mform->addHelpButton('timeclosestudents', 'sepugclosestudents', 'sepug');

		// cerrar
        $mform->addElement('date_time_selector', 'timeclose', get_string('sepugclose', 'sepug'),
            array('optional' => false));
		$mform->addHelpButton('timeclose', 'sepugclose', 'sepug');
		
		//-------------------------------------------------------------------------------
		// CONFIGURACION
        $mform->addElement('header', 'config', get_string('config', 'sepug'));
	
		// Obtenemos el nivel maximo de profundidad de las categorias
		$maxdepth = $DB->get_record_sql('SELECT MAX(depth) AS maxdepth FROM {course_categories}');    
		
		$options = array();
		for($i=1; $i<=$maxdepth->maxdepth; $i++){
			$options[$i] = get_string("level", "sepug")." ".$i;
		}

        $mform->addElement('select', 'depthlimit', get_string("depth_limit", "sepug"), $options);
		$mform->addHelpButton('depthlimit', 'depth_limit', 'sepug');
		
		// Obtenemos los nombres de las categorias de primer nivel
		$firstdepthcat = $DB->get_records("course_categories", array("depth"=>1));
		
		$options = array();
		foreach($firstdepthcat as $cat){
			$options[$cat->id] = $cat->name;
		}
		
		// Selector categoria de grado
		$mform->addElement('select', 'catgrado', get_string("catgrado", "sepug"), $options);
		$mform->addHelpButton('catgrado', 'catgrado', 'sepug');
		
		// Selector categoria de posgrado
		$mform->addElement('select', 'catposgrado', get_string("catposgrado", "sepug"), $options);
		$mform->addHelpButton('catposgrado', 'catposgrado', 'sepug');
		
		
		//-------------------------------------------------------------------------------
		// aniadir descripcion 
        $this->standard_coursemodule_elements();
		//-------------------------------------------------------------------------------
        // buttons
        $this->add_action_buttons();
    }


}

