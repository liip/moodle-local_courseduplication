<?php


class courseduplication_duplication_form extends moodleform{

    function definition() {
        global $CFG;
        $mform =& $this->_form;
        $mform->addElement('hidden', 'id'/*,$this->_customdata['courseid']*/);
        $mform->addElement('select', 'categoryid',
            get_string('targetcategory', 'local_courseduplication'),
            make_categories_options()/*,
            array('selected' => $this->_customdata['categoryid'])*/
        );
        $mform->setType('categoryid', PARAM_INT);
        $mform->addRule('categoryid', get_string('errormissingcategory', 'local_courseduplication'),
            'required', null, 'client');
        $this->add_action_buttons(true, get_string('duplicate', 'local_courseduplication'));
    }

}