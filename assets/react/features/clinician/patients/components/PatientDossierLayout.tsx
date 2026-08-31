import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { Tabs } from '@/react/components/Navigation/Tabs';
import { RightSidebar } from '@/react/components/Navigation/RightSidebar';
import { Avatar } from '@/react/components/UI/Avatar';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import { usePatientDossier } from '../hooks/usePatientDossier';
import { useMedicalRecord } from '../hooks/useMedicalRecord';
import { PatientDossierProvider } from '../contexts/PatientDossierContext';
import { PatientDossierCalendar } from './PatientDossierCalendar';
import { OverviewTab } from './tabs/OverviewTab';
import { MedicalProfileTab } from './tabs/MedicalProfileTab';
import { MeasurementsTab } from './tabs/MeasurementsTab';
import { PrescriptionsTab } from './tabs/PrescriptionsTab';
import { ConsultationsTab } from './tabs/ConsultationsTab';
import { NutritionTab } from './tabs/NutritionTab';
import { AppointmentsTab } from './tabs/AppointmentsTab';
import { NotesTab } from './tabs/NotesTab';
import { CommunicationsTab } from './tabs/CommunicationsTab';
import { AppointmentFormModal } from './modals/AppointmentFormModal';
import { MeasurementFormModal } from './modals/MeasurementFormModal';
import { MedicalNoteFormModal } from './modals/MedicalNoteFormModal';
import { PrescriptionFormModal } from './modals/PrescriptionFormModal';
import { MealFormModal } from './modals/MealFormModal';
import { AllergyFormModal } from './modals/AllergyFormModal';
import { DiagnosisFormModal } from './modals/DiagnosisFormModal';
import { MedicalConsentFormModal } from './modals/MedicalConsentFormModal';
import { EmergencyContactFormModal } from './modals/EmergencyContactFormModal';
import { PrescriptionItemFormModal } from './modals/PrescriptionItemFormModal';
import { PrescriptionVersionFormModal } from './modals/PrescriptionVersionFormModal';
import {
    DossierTabId,
    MeasurementPeriod,
    MeasurementTypeId,
    PatientAllergy,
    PatientDiagnosis,
    PatientEmergencyContact,
    PatientMedicalConsent,
    PatientPrescription,
} from '../types';

const DOSSIER_TABS = [
    { id: 'overview', label: "Vue d'ensemble" },
    { id: 'medical-profile', label: 'Profil médical' },
    { id: 'measurements', label: 'Mesures' },
    { id: 'prescriptions', label: 'Prescriptions' },
    { id: 'consultations', label: 'Consultations' },
    { id: 'nutrition', label: 'Nutrition' },
    { id: 'appointments', label: 'Rendez-vous' },
    { id: 'notes', label: 'Notes' },
    { id: 'communications', label: 'Communication' },
];

const PERIOD_TABS = [
    { id: '7d', label: '7 jours' },
    { id: '30d', label: '30 jours' },
    { id: '90d', label: '90 jours' },
    { id: 'all', label: 'Tout' },
];

const PERIOD_FILTER_TABS: DossierTabId[] = [
    'measurements',
    'prescriptions',
    'consultations',
    'nutrition',
    'appointments',
    'notes',
];

interface PatientDossierLayoutProps {
    patientId: string;
    mode: 'open' | 'closed';
}

export function PatientDossierLayout({ patientId, mode }: PatientDossierLayoutProps) {
    const navigate = useNavigate();
    const { pushAction } = useActionHistory();
    const { data, isLoading, error, reload } = usePatientDossier(patientId);
    const { isSaving, close, reopen } = useMedicalRecord(patientId);

    const [activeTab, setActiveTab] = useState<DossierTabId>('overview');
    const [period, setPeriod] = useState<MeasurementPeriod>('30d');
    const [selectedDate, setSelectedDate] = useState<Date | null>(null);

    const [measurementModalOpen, setMeasurementModalOpen] = useState(false);
    const [measurementModalType, setMeasurementModalType] = useState<MeasurementTypeId | undefined>(undefined);
    const [appointmentModalOpen, setAppointmentModalOpen] = useState(false);
    const [prescriptionModalOpen, setPrescriptionModalOpen] = useState(false);
    const [noteModalOpen, setNoteModalOpen] = useState(false);
    const [mealModalOpen, setMealModalOpen] = useState(false);
    const [allergyModalOpen, setAllergyModalOpen] = useState(false);
    const [editingAllergy, setEditingAllergy] = useState<PatientAllergy | undefined>(undefined);
    const [diagnosisModalOpen, setDiagnosisModalOpen] = useState(false);
    const [editingDiagnosis, setEditingDiagnosis] = useState<PatientDiagnosis | undefined>(undefined);
    const [consentModalOpen, setConsentModalOpen] = useState(false);
    const [editingConsent, setEditingConsent] = useState<PatientMedicalConsent | undefined>(undefined);
    const [emergencyContactModalOpen, setEmergencyContactModalOpen] = useState(false);
    const [editingEmergencyContact, setEditingEmergencyContact] = useState<PatientEmergencyContact | undefined>(undefined);
    const [prescriptionItemModalOpen, setPrescriptionItemModalOpen] = useState(false);
    const [prescriptionVersionModalOpen, setPrescriptionVersionModalOpen] = useState(false);
    const [selectedPrescription, setSelectedPrescription] = useState<PatientPrescription | null>(null);

    const handleTabChange = (tabId: string) => {
        const previous = activeTab;
        setActiveTab(tabId as DossierTabId);
        setSelectedDate(null);
        pushAction(() => {
            setActiveTab(previous);
            setSelectedDate(null);
        });
    };

    const handlePeriodChange = (newPeriod: string) => {
        const previous = period;
        setPeriod(newPeriod as MeasurementPeriod);
        setSelectedDate(null);
        pushAction(() => setPeriod(previous));
    };

    const handleDateSelect = (date: Date) => {
        const previous = selectedDate;
        setSelectedDate(date);
        pushAction(() => setSelectedDate(previous));
    };

    const handleClose = async () => {
        const success = await close();
        if (success) {
            navigate(`/clinician/patients/${patientId}/record/closed`);
        }
    };

    const handleReopen = async () => {
        const success = await reopen();
        if (success) {
            navigate(`/clinician/patients/${patientId}/record`);
        }
    };

    useEffect(() => {
        if (!data) return;
        if (!data.record) {
            navigate(`/clinician/patients/${patientId}/record/init`, { replace: true });
            return;
        }
        if (mode === 'open' && data.record.status === 'closed') {
            navigate(`/clinician/patients/${patientId}/record/closed`, { replace: true });
        }
        if (mode === 'closed' && data.record.status === 'open') {
            navigate(`/clinician/patients/${patientId}/record`, { replace: true });
        }
    }, [data, mode, navigate, patientId]);

    if (isLoading) return <Spinner />;
    if (error || !data) return <Alert variant="error">{error ?? 'Dossier introuvable.'}</Alert>;

    const isReadOnly = mode === 'closed';

    const openMeasurementModal = (type?: MeasurementTypeId) => {
        setMeasurementModalType(type);
        setMeasurementModalOpen(true);
    };

    const closeAndReload = (setter: (open: boolean) => void) => {
        setter(false);
        reload();
    };

    const renderTabContent = () => {
        switch (activeTab) {
            case 'overview':
                return <OverviewTab />;
            case 'medical-profile':
                return <MedicalProfileTab />;
            case 'measurements':
                return <MeasurementsTab />;
            case 'prescriptions':
                return <PrescriptionsTab />;
            case 'consultations':
                return <ConsultationsTab />;
            case 'nutrition':
                return <NutritionTab />;
            case 'appointments':
                return <AppointmentsTab />;
            case 'notes':
                return <NotesTab />;
            case 'communications':
                return <CommunicationsTab />;
            default:
                return null;
        }
    };

    const versionCount = selectedPrescription
        ? data.prescriptionVersions.filter((v) => v.prescriptionId === selectedPrescription.id).length
        : 0;

    return (
        <PatientDossierProvider
            value={{
                patientId,
                data,
                reload,
                isReadOnly,
                selectedDate,
                setSelectedDate,
                period,
                setPeriod,
                openMeasurementModal,
                openAppointmentModal: () => setAppointmentModalOpen(true),
                openPrescriptionModal: () => setPrescriptionModalOpen(true),
                openNoteModal: () => setNoteModalOpen(true),
                openMealModal: () => setMealModalOpen(true),
                openAllergyModal: (allergy) => {
                    setEditingAllergy(allergy);
                    setAllergyModalOpen(true);
                },
                openDiagnosisModal: (diagnosis) => {
                    setEditingDiagnosis(diagnosis);
                    setDiagnosisModalOpen(true);
                },
                openConsentModal: (consent) => {
                    setEditingConsent(consent);
                    setConsentModalOpen(true);
                },
                openEmergencyContactModal: (contact) => {
                    setEditingEmergencyContact(contact);
                    setEmergencyContactModalOpen(true);
                },
                openPrescriptionItemModal: (prescription) => {
                    setSelectedPrescription(prescription);
                    setPrescriptionItemModalOpen(true);
                },
                openPrescriptionVersionModal: (prescription) => {
                    setSelectedPrescription(prescription);
                    setPrescriptionVersionModalOpen(true);
                },
            }}
        >
            <div className="clinician-record-page">
                <div className="clinician-record-page__header">
                    <div className="clinician-record-page__patient">
                        <Avatar
                            src={data.profile.avatarUrl}
                            name={data.profile.fullName}
                            size="xlarge"
                            shape="circle"
                        />
                        <div>
                            <h1>{data.profile.fullName}</h1>
                            <p>Dossier médical — {data.profile.organizationName ?? 'Organisation'}</p>
                            <div className="clinician-record-page__badges">
                                <Badge variant={isReadOnly ? 'warning' : 'success'}>
                                    {isReadOnly ? 'Dossier fermé' : 'Dossier ouvert'}
                                </Badge>
                                <Badge variant={data.profile.status?.toUpperCase() === 'ACTIVE' ? 'success' : 'error'}>
                                    {data.profile.status?.toUpperCase() === 'ACTIVE' ? 'Actif' : 'Inactif'}
                                </Badge>
                            </div>
                        </div>
                    </div>

                    <div className="clinician-record-page__actions">
                        <Button variant="secondary" onClick={() => navigate('/clinician/my-patients')}>
                            Retour aux patients
                        </Button>
                        {isReadOnly ? (
                            <Button variant="primary" onClick={handleReopen} disabled={isSaving}>
                                {isSaving ? 'Réouverture...' : 'Rouvrir le dossier'}
                            </Button>
                        ) : (
                            <Button variant="danger" onClick={handleClose} disabled={isSaving}>
                                {isSaving ? 'Fermeture...' : 'Fermer le dossier'}
                            </Button>
                        )}
                    </div>
                </div>

                <Tabs tabs={DOSSIER_TABS} defaultActiveTabId={activeTab} onChange={handleTabChange} />

                {PERIOD_FILTER_TABS.includes(activeTab) && (
                    <Tabs tabs={PERIOD_TABS} defaultActiveTabId={period} onChange={handlePeriodChange} />
                )}

                <div className="clinician-record-page__body">
                    <div className="clinician-record-page__content">
                        {renderTabContent()}
                    </div>

                    <RightSidebar
                        collapsible
                        size="medium"
                        minWidth={250}
                        maxWidth={400}
                        closeThreshold={80}
                        collapsedWidth={35}
                        title="Calendrier"
                        header={<div>Naviguez par date</div>}
                    >
                        <div className="clinician-record-page__right-content">
                            <PatientDossierCalendar
                                data={data}
                                activeTab={activeTab}
                                selectedDate={selectedDate}
                                onDateSelect={handleDateSelect}
                            />
                            {selectedDate && (
                                <Button
                                    variant="secondary"
                                    size="small"
                                    onClick={() => setSelectedDate(null)}
                                >
                                    Effacer la sélection
                                </Button>
                            )}
                        </div>
                    </RightSidebar>
                </div>
            </div>

            <MeasurementFormModal
                isOpen={measurementModalOpen}
                onClose={() => setMeasurementModalOpen(false)}
                patientId={patientId}
                initialType={measurementModalType}
                onSuccess={() => closeAndReload(setMeasurementModalOpen)}
            />
            <AppointmentFormModal
                isOpen={appointmentModalOpen}
                onClose={() => setAppointmentModalOpen(false)}
                data={data}
                defaultDate={selectedDate}
                onSuccess={() => closeAndReload(setAppointmentModalOpen)}
            />
            <MedicalNoteFormModal
                isOpen={noteModalOpen}
                onClose={() => setNoteModalOpen(false)}
                data={data}
                defaultDate={selectedDate}
                onSuccess={() => closeAndReload(setNoteModalOpen)}
            />
            <PrescriptionFormModal
                isOpen={prescriptionModalOpen}
                onClose={() => setPrescriptionModalOpen(false)}
                data={data}
                onSuccess={() => closeAndReload(setPrescriptionModalOpen)}
            />
            <MealFormModal
                isOpen={mealModalOpen}
                onClose={() => setMealModalOpen(false)}
                data={data}
                onSuccess={() => closeAndReload(setMealModalOpen)}
            />
            <AllergyFormModal
                isOpen={allergyModalOpen}
                onClose={() => { setAllergyModalOpen(false); setEditingAllergy(undefined); }}
                data={data}
                allergy={editingAllergy}
                onSuccess={() => closeAndReload(setAllergyModalOpen)}
            />
            <DiagnosisFormModal
                isOpen={diagnosisModalOpen}
                onClose={() => { setDiagnosisModalOpen(false); setEditingDiagnosis(undefined); }}
                data={data}
                diagnosis={editingDiagnosis}
                onSuccess={() => closeAndReload(setDiagnosisModalOpen)}
            />
            <MedicalConsentFormModal
                isOpen={consentModalOpen}
                onClose={() => { setConsentModalOpen(false); setEditingConsent(undefined); }}
                data={data}
                consent={editingConsent}
                onSuccess={() => closeAndReload(setConsentModalOpen)}
            />
            <EmergencyContactFormModal
                isOpen={emergencyContactModalOpen}
                onClose={() => { setEmergencyContactModalOpen(false); setEditingEmergencyContact(undefined); }}
                data={data}
                contact={editingEmergencyContact}
                onSuccess={() => closeAndReload(setEmergencyContactModalOpen)}
            />
            <PrescriptionItemFormModal
                isOpen={prescriptionItemModalOpen}
                onClose={() => { setPrescriptionItemModalOpen(false); setSelectedPrescription(null); }}
                prescription={selectedPrescription}
                onSuccess={() => closeAndReload(setPrescriptionItemModalOpen)}
            />
            <PrescriptionVersionFormModal
                isOpen={prescriptionVersionModalOpen}
                onClose={() => { setPrescriptionVersionModalOpen(false); setSelectedPrescription(null); }}
                prescription={selectedPrescription}
                currentVersionCount={versionCount}
                onSuccess={() => closeAndReload(setPrescriptionVersionModalOpen)}
            />
        </PatientDossierProvider>
    );
}
