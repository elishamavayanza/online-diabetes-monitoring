import { useState } from 'react';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Card } from '@/react/components/UI/Card';
import { Button } from '@/react/components/UI/Button';
import { useOrganizationReport } from '../hooks/useOrganizationReport';
import { PeriodSelector } from '../components/PeriodSelector';
import { ReportStatCard } from '../components/ReportStatCard';
import { DistributionChart } from '../components/DistributionChart';
import { TrendChart } from '../components/TrendChart';
import { DownloadReportModal } from '../components/DownloadReportModal';
import '@/styles/pages/admin/reports/_reports.scss';

export function OrganizationReportPage() {
    const { data, isLoading, error, filters, setPeriod, setCustomRange } = useOrganizationReport('month');
    const [isDownloadOpen, setIsDownloadOpen] = useState(false);

    if (isLoading) {
        return <Spinner />;
    }

    if (error || !data) {
        return <Alert variant="error">{error ?? 'Rapport indisponible.'}</Alert>;
    }

    return (
        <div className="organization-report-page">
            <header className="organization-report-page__header">
                <div>
                    <h1>Rapport organisation</h1>
                    <p>{data.organizationName} — du {data.period.from} au {data.period.to}</p>
                </div>
                <div className="organization-report-page__actions">
                    <PeriodSelector
                        activePeriod={filters.period}
                        customFrom={filters.from}
                        customTo={filters.to}
                        onPeriodChange={setPeriod}
                        onCustomRangeChange={setCustomRange}
                    />
                    <Button variant="secondary" onClick={() => setIsDownloadOpen(true)}>
                        Télécharger en PDF
                    </Button>
                </div>
            </header>

            <DownloadReportModal
                isOpen={isDownloadOpen}
                onClose={() => setIsDownloadOpen(false)}
                report={data}
            />

            <section className="organization-report-page__section">
                <h2>Patients & démographie</h2>
                <div className="organization-report-page__stats">
                    <ReportStatCard label="Patients total" stat={data.demographics.totalPatients} />
                    <ReportStatCard label="Patients actifs" stat={data.demographics.activePatients} />
                    <ReportStatCard label="Nouveaux patients" stat={data.demographics.newPatients} />
                </div>
                <div className="organization-report-page__grid">
                    <Card><DistributionChart title="Répartition par genre" items={data.demographics.genderDistribution} /></Card>
                    <Card><DistributionChart title="Tranches d'âge" items={data.demographics.ageGroups} /></Card>
                </div>
            </section>

            <section className="organization-report-page__section">
                <h2>État de santé</h2>
                <div className="organization-report-page__stats">
                    <ReportStatCard label="Glycémie moyenne" stat={data.healthStatus.averageGlucose} />
                    <ReportStatCard label="Mesures glycémie" stat={data.healthStatus.glucoseMeasurements} />
                    <ReportStatCard label="HbA1c moyenne" stat={data.healthStatus.averageHbA1c} />
                    <ReportStatCard label="Tension systolique" stat={data.healthStatus.averageSystolic} />
                    <ReportStatCard label="Tension diastolique" stat={data.healthStatus.averageDiastolic} />
                    <ReportStatCard label="IMC moyen" stat={data.healthStatus.averageBmi} />
                    <ReportStatCard label="Poids moyen" stat={data.healthStatus.averageWeightKg} />
                </div>
                <Card>
                    <DistributionChart title="Répartition glycémique" items={data.healthStatus.glucoseRanges} />
                </Card>
            </section>

            <section className="organization-report-page__section">
                <h2>Activité médicale</h2>
                <div className="organization-report-page__stats">
                    <ReportStatCard label="Rendez-vous" stat={data.medicalActivity.totalAppointments} />
                    <ReportStatCard label="Terminés" stat={data.medicalActivity.completedAppointments} />
                    <ReportStatCard label="Annulés" stat={data.medicalActivity.cancelledAppointments} />
                    <ReportStatCard label="Diagnostics" stat={data.medicalActivity.diagnosesCount} />
                    <ReportStatCard label="Dossiers ouverts" stat={data.medicalActivity.openMedicalRecords} />
                    <ReportStatCard label="Dossiers fermés" stat={data.medicalActivity.closedMedicalRecords} />
                </div>
                <Card>
                    <DistributionChart title="Rendez-vous par statut" items={data.medicalActivity.appointmentsByStatus} />
                </Card>
            </section>

            <section className="organization-report-page__section">
                <h2>Traitements & observance</h2>
                <div className="organization-report-page__stats">
                    <ReportStatCard label="Prescriptions actives" stat={data.treatments.activePrescriptions} />
                    <ReportStatCard label="Nouvelles prescriptions" stat={data.treatments.newPrescriptions} />
                    <ReportStatCard label="Taux d'observance" stat={data.treatments.adherenceRate} />
                    <ReportStatCard label="Prises enregistrées" stat={data.treatments.totalIntakes} />
                </div>
                <Card>
                    <DistributionChart title="Prises par statut" items={data.treatments.intakesByStatus} />
                </Card>
            </section>

            <section className="organization-report-page__section">
                <h2>Nutrition & activité physique</h2>
                <div className="organization-report-page__stats">
                    <ReportStatCard label="Repas enregistrés" stat={data.lifestyle.totalMeals} />
                    <ReportStatCard label="Séances d'activité" stat={data.lifestyle.physicalActivitySessions} />
                    <ReportStatCard label="Minutes d'activité" stat={data.lifestyle.totalActivityMinutes} />
                    <ReportStatCard label="Durée moyenne" stat={data.lifestyle.averageActivityMinutes} />
                </div>
                <Card>
                    <DistributionChart title="Repas par type" items={data.lifestyle.mealsByType} />
                </Card>
            </section>

            <section className="organization-report-page__section">
                <h2>Tendances & indicateurs</h2>
                <div className="organization-report-page__stats">
                    <ReportStatCard label="Patients avec mesures" stat={data.trends.patientsWithMeasurements} />
                    <ReportStatCard label="Taux de suivi" stat={data.trends.measurementComplianceRate} />
                </div>
                <div className="organization-report-page__grid">
                    {data.trends.series.map((series) => (
                        <Card key={series.label}>
                            <TrendChart series={series} />
                        </Card>
                    ))}
                </div>
            </section>
        </div>
    );
}
