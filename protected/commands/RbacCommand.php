<?php
class RbacCommand extends CConsoleCommand
{
    private $_authManager;

    public function getHelp()
    {
        $description = "DESCRIPTION\n";
        $description .= '    '. "This command generates an initial RBAC authorization hierarchy\n";
        $description .= '    '. "for the Rinkfinder Web Application.\n";

        return parent::getHelp() . $description;
    }

    /**
     * The default action - create the RBAC structure.
     */
    public function actionIndex()
    {
        $this->ensureAuthManagerDefined();

        // provide the oportunity for the use to abort the request
        $message = "WARNING! This command will erase the existing RBAC structure!\n\n";
        $message .= "This command will create eight roles:\n";
        $message .= "Authorization Assignment Manager, Authorization Items Manager,\n";
        $message .= "Authorization System Manager, \n";
        $message .= "Site Administrator, Application Administrator, Arena Manager,\n";
        $message .= "Restricted Arena Manager, and User.\n\n";
        $message .= "This command will also create the supporting tasks and operations\n";
        $message .= "for the roles above.\n\n";
        $message .= "Would you like to continue?";

        // check the input from the user and continue if 
        // they indicated yes to the above question
        if($this->confirm($message)) {
            // first we need to remove all operations, 
            // roles, child relationship and assignments
            $this->_authManager->clearAll();

            $this->createAuthObjects();            
            $this->createUserObjects();            
            $this->createArenaObjects();
            $this->createIceSheetObjects();
            $this->createEventObjects();
            $this->createEventRequestObjects();
            $this->createReservationObjects();
            $this->createContactObjects();
            $this->createLookupObjects();
            
            $this->createRoles();
            
            // ensure we have at least one admin user by assigning the
            // default sysadmin account to the Administrator role
            $this->_authManager->assign("Administrator", 1);

            // provide a message indicating success
            echo "Authorization hierarchy successfully generated.\n";
        } else {
            echo "Operation cancelled.\n";
        }
    }

    public function actionDelete()
    {
        $this->ensureAuthManagerDefined();

        // provide the oportunity for the use to abort the request
        $message = "This command will clear all RBAC definitions.\n";
        $message .= "Would you like to continue?";

        // check the input from the user and continue if
        // they indicated yes to the above question
        if($this->confirm($message)) {
            $this->_authManager->clearAll();
            echo "Authorization hierarchy removed.\n";
        } else {
            echo "Delete operation cancelled.\n";
        }			
    }
    
    protected function ensureAuthManagerDefined()
    {
        // ensure that an authManager is defined as this is mandatory
        // for creating an auth heirarchy
        if(($this->_authManager = Yii::app()->authManager) === null) {
            $message = "Error: an authorization manager, named 'authManager'\n";
            $message .= "must be configured to use this command.";
            $this->usageError($message);
        }
    }

    protected function getSiteAdministratorBizRule()
    {
        $bizRule = 'return Yii::app()->user->isSiteAdministrator();';        

        return $bizRule;
    }

    protected function getApplicationAdministratorBizRule()
    {
        $bizRule = 'return Yii::app()->user->isApplicationAdministrator();';        

        return $bizRule;
    }

    protected function getManagerBizRule()
    {
        $bizRule = 'return isset($params["arena"]) && isset($params["user"]) && ';
        $bizRule .= '$params["user"]->isArenaManager($params["arena"]->id);';

        return $bizRule;
    }

    protected function getRestrictedManagerBizRule()
    {
        $bizRule = 'return isset($params["arena"]) && isset($params["user"]) && ';
        $bizRule .= '$params["user"]->isArenaRestrictedManager($params["arena"]->id);';

        return $bizRule;
    }

    protected function getUserBizRule()
    {
        $bizRule = 'return isset($params["user"]) && $params["user"]->id == Yii::app()->user->id;';

        return $bizRule;
    }

    protected function createAuthObjects()
    {
        // create the authorization management roles
        $this->_authManager->createRole("authManageAssignments", "Manage RBAC Authorization Assignments.");
        $this->_authManager->createRole("authManageItems", "Manage RBAC Authorization Items.");
        $role = $this->_authManager->createRole("authManageRbac", "Manage RBAC Authorization System.");
        $role->addChild('authManageAssignments');
        $role->addChild('authManageItems');
    }

    protected function createUserObjects()
    {
        // create the lowest level operations for users
        $this->_authManager->createOperation("createUser", "Create a new user.");
        $this->_authManager->createOperation("uploadUser", "Create new user(s) from bulk import.");
        $this->_authManager->createOperation("viewUser", "View user and profile information.");
        $this->_authManager->createOperation("updateUser", "Update a users information.");
        $this->_authManager->createOperation("assignUser", "Assign a user to an Arena.");
        $this->_authManager->createOperation("removeUser", "Remove a user from an Arena.");
        $this->_authManager->createOperation("deleteUser", "Remove a user from the site.");
        $this->_authManager->createOperation("indexUser", "List user(s).");
        $this->_authManager->createOperation("adminUser", "Manage all users.");

        $task = $this->_authManager->createTask("manageAssignedUsers", "Manage all users assigned to the arena.", $this->managerBizRule);
        $task->addChild('viewUser');
        $task->addChild('updateUser');
        $task->addChild('removeUser');
        $task->addChild('indexUser');

        $task = $this->_authManager->createTask("assignRestrictedManager", "Assign a sub manager to the arena.", $this->managerBizRule);
        $task->addChild('createUser');
        $task->addChild('assignUser');

        $task = $this->_authManager->createTask("manageOwnUser", "Manage your own user account.", $this->userBizRule);
        $task->addChild('viewUser');
        $task->addChild('updateUser');
        $task->addChild('indexUser');

        $task = $this->_authManager->createTask("assignManager", "Assign a manager to the arena.", $this->applicationAdministratorBizRule);
        $task->addChild('createUser');
        $task->addChild('assignUser');

        $task = $this->_authManager->createTask("assignApplicationAdministrator", "Assign an application Administrator.", $this->siteAdministratorBizRule);
        $task->addChild('createUser');
        $task->addChild('assignUser');
        
        $task = $this->_authManager->createTask("administerAllUsers", "Manage all the application users.", $this->applicationAdministratorBizRule);
        $task->addChild('uploadUser');
        $task->addChild('deleteUser');
        $task->addChild('adminUser');
    }

    protected function createArenaObjects()
    {
        // create the lowest level operations for arenas
        $this->_authManager->createOperation("createArena", "Create a new arena.");
        $this->_authManager->createOperation("uploadArena", "Create new arena(s) from bulk import.");
        $this->_authManager->createOperation("readArena", "Read arena information.");
        $this->_authManager->createOperation("updateArena", "Update arena information.");
        $this->_authManager->createOperation("geocodeArena", "Geocode an arena.");
        $this->_authManager->createOperation("deleteArena", "Delete an arena.");
        $this->_authManager->createOperation("indexArena", "List arena(s).");
        $this->_authManager->createOperation("adminArena", "Manage all arenas.");
        
        // create the task level operations for arenas
        $task = $this->_authManager->createTask("administerAllArenas", "Manage all the arenas.", $this->applicationAdministratorBizRule);
        $task->addChild('createArena');
        $task->addChild('uploadArena');
        $task->addChild('readArena');
        $task->addChild('updateArena');
        $task->addChild('geocodeArena');
        $task->addChild('deleteArena');
        $task->addChild('indexArena');
        $task->addChild('adminArena');
    }

    protected function createIceSheetObjects()
    {
        // create the lowest level operations for ice sheets
        $this->_authManager->createOperation("createIceSheet", "Create a new ice sheet.");
        $this->_authManager->createOperation("readIceSheet", "Read ice sheet information.");
        $this->_authManager->createOperation("updateIceSheet", "Update ice sheet information.");
        $this->_authManager->createOperation("deleteIceSheet", "Delete an ice sheet.");
        $this->_authManager->createOperation("indexIceSheet", "List ice sheet(s).");
        $this->_authManager->createOperation("adminIceSheet", "Manage all ice sheets.");
        
        // create the task level operations for ice sheets
        $task = $this->_authManager->createTask("administerAllIceSheets", "Manage all the ice sheets.", $this->applicationAdministratorBizRule);
        $task->addChild('createIceSheet');
        $task->addChild('readIceSheet');
        $task->addChild('updateIceSheet');
        $task->addChild('deleteIceSheet');
        $task->addChild('indexIceSheet');
        $task->addChild('adminIceSheet');
    }

    protected function createEventObjects()
    {
        // create the lowest level operations for events
        $this->_authManager->createOperation("createEvent", "Create a new event.");
        $this->_authManager->createOperation("uploadEvent", "Create new event(s) from bulk import.");
        $this->_authManager->createOperation("readEvent", "Read event information.");
        $this->_authManager->createOperation("updateEvent", "Update event information.");
        $this->_authManager->createOperation("deleteEvent", "Delete an event.");
        $this->_authManager->createOperation("indexEvent", "List event(s).");
        $this->_authManager->createOperation("adminEvent", "Manage all events.");
        
        // create the task level operations for events
        $task = $this->_authManager->createTask("administerAllEvents", "Manage all the events.", $this->applicationAdministratorBizRule);
        $task->addChild('createEvent');
        $task->addChild('uploadEvent');
        $task->addChild('readEvent');
        $task->addChild('updateEvent');
        $task->addChild('deleteEvent');
        $task->addChild('indexEvent');
        $task->addChild('adminEvent');
    }

    protected function createEventRequestObjects()
    {
        // create the lowest level operations for event requests
        $this->_authManager->createOperation("createEventRequest", "Create a new event request.");
        $this->_authManager->createOperation("readEventRequest", "Read event request information.");
        $this->_authManager->createOperation("updateEventRequest", "Update event request information.");
        $this->_authManager->createOperation("deleteEventRequest", "Delete an event request.");
        $this->_authManager->createOperation("indexEventRequest", "List event request(s).");
        $this->_authManager->createOperation("adminEventRequest", "Manage all event requests.");
        
        // create the task level operations for events
        $task = $this->_authManager->createTask("administerAllEventRequests", "Manage all the event requests.", $this->applicationAdministratorBizRule);
        $task->addChild('createEventRequest');
        $task->addChild('readEventRequest');
        $task->addChild('updateEventRequest');
        $task->addChild('deleteEventRequest');
        $task->addChild('indexEventRequest');
        $task->addChild('adminEventRequest');
    }

    protected function createReservationObjects()
    {
        // create the lowest level operations for reservations
        $this->_authManager->createOperation("createReservation", "Create a new reservation.");
        $this->_authManager->createOperation("readReservation", "Read reservation information.");
        $this->_authManager->createOperation("updateReservation", "Update reservation information.");
        $this->_authManager->createOperation("deleteReservation", "Delete a reservation.");
        $this->_authManager->createOperation("indexReservation", "List reservation(s).");
        $this->_authManager->createOperation("adminReservation", "Manage all reservations.");
        
        // create the task level operations for reservations
        $task = $this->_authManager->createTask("administerAllReservations", "Manage all the reservations.", $this->applicationAdministratorBizRule);
        $task->addChild('createReservation');
        $task->addChild('readReservation');
        $task->addChild('updateReservation');
        $task->addChild('deleteReservation');
        $task->addChild('indexReservation');
        $task->addChild('adminReservation');
    }

    protected function createContactObjects()
    {
        // create the lowest level operations for contacts
        $this->_authManager->createOperation("createContact", "Create a new contact.");
        $this->_authManager->createOperation("readContact", "Read contact information.");
        $this->_authManager->createOperation("updateContact", "Update contact information.");
        $this->_authManager->createOperation("assignContact", "Assign a contact to an Arena.");
        $this->_authManager->createOperation("removeContact", "Remove a contact from an Arena.");
        $this->_authManager->createOperation("deleteContact", "Delete a contact.");
        $this->_authManager->createOperation("indexContact", "List contact(s).");
        $this->_authManager->createOperation("adminContact", "Manage all contacts.");
        
        // create the task level operations for contacta
        $task = $this->_authManager->createTask("administerAllContactss", "Manage all the contacts.", $this->applicationAdministratorBizRule);
        $task->addChild('createContact');
        $task->addChild('readContact');
        $task->addChild('updateContact');
        $task->addChild('assignContact');
        $task->addChild('removeContact');
        $task->addChild('deleteContact');
        $task->addChild('indexContact');
        $task->addChild('adminContact');
    }

    protected function createLookupObjects()
    {
        // create the lowest level operations for lookups
        $this->_authManager->createOperation("createLookup", "Create a new lookup.");
        $this->_authManager->createOperation("readLookup", "Read lookup information.");
        $this->_authManager->createOperation("updateLookup", "Update lookup information.");
        $this->_authManager->createOperation("deleteLookup", "Delete a lookup.");
        $this->_authManager->createOperation("indexLookup", "List lookup(s).");
        $this->_authManager->createOperation("adminLookup", "Manage all lookups.");
        
        // create the task level operations for lookups
        $task = $this->_authManager->createTask("administerAllLookups", "Manage all the lookups.", $this->applicationAdministratorBizRule);
        $task->addChild('createLookup');
        $task->addChild('readLookup');
        $task->addChild('updateLookup');
        $task->addChild('deleteLookup');
        $task->addChild('indexLookup');
        $task->addChild('adminLookup');
    }

    protected function createRoles()
    {
            // create the user role and add the appropriate 
            // permissions as children to this role
            $role = $this->_authManager->createRole("User", "A regular user account.");
            $role->addChild("manageOwnUser");

            // create the restricted manager role, and add the appropriate 
            // permissions, as well as the user role itself, as children
            $role = $this->_authManager->createRole("RestrictedManager", "An Arena Manager with fewer permissions than a regular Arena Manager.");
            $role->addChild("User");

            // create the manager role, and add the appropriate permissions, 
            // as well as both the user and restricted manager roles as children
            $role = $this->_authManager->createRole("Manager", "An Arena Manager with full permissions to manage their assigned Arenas.");
            $role->addChild("User");
            $role->addChild("RestrictedManager");
            $role->addChild("manageAssignedUsers");
            $role->addChild("assignRestrictedManager");
		
            // create the admin role, and add the appropriate permissions, 
            // as well as the user, restricted manager, and manager roles as children
            $role = $this->_authManager->createRole("ApplicationAdministrator", "An Administrator account with fewer permissions than a Site Administrator.");
            $role->addChild("User");
            $role->addChild("RestrictedManager");
            $role->addChild("Manager");
            $role->addChild("assignManager");
            $role->addChild("administerAllUsers");
            
            // create the admin role, and add the appropriate permissions, 
            // as well as the user, restricted manager, and manager roles as children
            $role = $this->_authManager->createRole("Administrator", "A Site Administrator with full permissions to control everything!");
            $role->addChild("authManageRbac");
            $role->addChild("User");
            $role->addChild("RestrictedManager");
            $role->addChild("Manager");
            $role->addChild("ApplicationAdministrator");
            $role->addChild("assignApplicationAdministrator");
    }
}