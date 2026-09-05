<?php

namespace App\Security;

enum SecurityAction: string
{
    // Général
    case VIEW = 'view';
    case CREATE = 'create';
    case UPDATE = 'update';
    case DELETE = 'delete';

    // Organisation
    case MANAGE_ORGANIZATION = 'manage_organization';
    case MANAGE_FACILITY = 'manage_facility';
    case MANAGE_DEPARTMENT = 'manage_department';

    // Utilisateurs
    case MANAGE_USERS = 'manage_users';
    case SUSPEND_USER = 'suspend_user';
    case ACTIVATE_USER = 'activate_user';

    // Patients
    case VIEW_PATIENT = 'view_patient';
    case UPDATE_PATIENT = 'update_patient';
    case TRANSFER_PATIENT = 'transfer_patient';
    case ARCHIVE_PATIENT = 'archive_patient';
    case ACTIVATE_PATIENT = 'activate_patient';
    case CREATE_ALLERGY = 'create_allergy';
    case VIEW_ALLERGY = 'view_allergy';
    case UPDATE_ALLERGY = 'update_allergy';
    case DELETE_ALLERGY = 'delete_allergy';

    // Dossier médical
    case VIEW_MEDICAL_RECORD = 'view_medical_record';
    case CREATE_MEDICAL_RECORD = 'create_medical_record';
    case VIEW_MEDICAL_NOTES = 'view_medical_notes';
    case EDIT_MEDICAL_NOTE = 'edit_medical_note';
    case DELETE_MEDICAL_NOTE = 'delete_medical_note';
    case CREATE_DIAGNOSIS = 'create_diagnosis';
    case UPDATE_DIAGNOSIS = 'update_diagnosis';

    case CLOSE_MEDICAL_RECORD = 'close_medical_record';
    case CREATE_MEDICAL_NOTE = 'create_medical_note';

    // Mesures
    case RECORD_GLUCOSE = 'record_glucose';
    case RECORD_BLOOD_PRESSURE = 'record_blood_pressure';
    case RECORD_HBA1C = 'record_hba1c';
    case RECORD_WEIGHT = 'record_weight';
    case RECORD_ACTIVITY = 'record_activity';
    case VIEW_MEASUREMENTS = 'view_measurements';

    // Laboratoire
    case VIEW_LABORATORY_RESULT = 'view_laboratory_result';
    case UPLOAD_LABORATORY_RESULT = 'upload_laboratory_result';

    // Traitement
    case VIEW_PRESCRIPTION = 'view_prescription';
    case CREATE_PRESCRIPTION = 'create_prescription';
    case UPDATE_PRESCRIPTION = 'update_prescription';
    case CANCEL_PRESCRIPTION = 'cancel_prescription';
    case VALIDATE_PRESCRIPTION = 'validate_prescription';
    case RECORD_MEDICATION_INTAKE = 'record_medication_intake';
    case DELETE_MEDICATION_INTAKE = 'delete_medication_intake';
    case MANAGE_MEDICATION = 'manage_medication';

    // Nutrition
    case MANAGE_FOOD = 'manage_food';
    case MANAGE_FOOD_CATEGORY = 'manage_food_category';
    case MANAGE_MEAL = 'manage_meal';
    case VIEW_NUTRITION = 'view_nutrition';
    case CREATE_NUTRITION_ADVICE = 'create_nutrition_advice';

    // Rendez-vous
    case VIEW_APPOINTMENT = 'view_appointment';
    case CREATE_APPOINTMENT = 'create_appointment';
    case UPDATE_APPOINTMENT = 'update_appointment';
    case DELETE_APPOINTMENT = 'delete_appointment';
    case CANCEL_APPOINTMENT = 'cancel_appointment';
    case CONFIRM_APPOINTMENT = 'confirm_appointment';
    case REQUEST_RESCHEDULE = 'request_reschedule';

    // Rappels de rendez-vous (Ajouté)
    case VIEW_APPOINTMENT_REMINDER = 'view_appointment_reminder';
    case CREATE_APPOINTMENT_REMINDER = 'create_appointment_reminder';
    case UPDATE_APPOINTMENT_REMINDER = 'update_appointment_reminder';
    case DELETE_APPOINTMENT_REMINDER = 'delete_appointment_reminder';

    // Communication
    case SEND_MESSAGE = 'send_message';
    case READ_MESSAGE = 'read_message';
    case DOWNLOAD_ATTACHMENT = 'download_attachment';
    case CREATE_CONVERSATION = 'create_conversation';

    // Notifications
    case VIEW_NOTIFICATION = 'view_notification';
    case CREATE_NOTIFICATION = 'create_notification';

    case CREATE_REMINDER_RULE = 'create_reminder_rule';
    case MARK_NOTIFICATION_READ = 'mark_notification_read';


    // Audit
    case VIEW_AUDIT_LOG = 'view_audit_log';
    case VIEW_DATA_ACCESS_LOG = 'view_data_access_log';
    case CREATE_DATA_ACCESS_LOG = 'create_data_access_log';

    // Administration
    case MANAGE_ROLES = 'manage_roles';
    case MANAGE_PERMISSIONS = 'manage_permissions';

    // Contacts d'urgence
    case CREATE_EMERGENCY_CONTACT = 'create_emergency_contact';
    case VIEW_EMERGENCY_CONTACT = 'view_emergency_contact';
    case UPDATE_EMERGENCY_CONTACT = 'update_emergency_contact';
    case DELETE_EMERGENCY_CONTACT = 'delete_emergency_contact';

    // Consentements médicaux
    case CREATE_MEDICAL_CONSENT = 'create_medical_consent';
    case VIEW_MEDICAL_CONSENT = 'view_medical_consent';
    case REVOKE_MEDICAL_CONSENT = 'revoke_medical_consent';


    case VIEW_MEDICATION = 'view_medication';
}
