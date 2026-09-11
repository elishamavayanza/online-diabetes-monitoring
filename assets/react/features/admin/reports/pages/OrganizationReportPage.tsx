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
import { useI18n } from '@/react/i18n/I18nContext';
import '@/styles/pages/admin/reports/_reports.scss';

export function OrganizationReportPage() {
    const { data, isLoading, error, filters, setPeriod, setCustomRange } = useOrganizationReport('month');
    const [isDownloadOpen, setIsDownloadOpen] = useState(false);
    const { t } = useI18n();

    if (isLoading) {
        return <Spinner />;
    }

    if (error || !data) {
        return <Alert variant="error">{error ?? t('Rapport indisponible.')}</Alert>;
    }

    return (
        <div className="organization-report-page">
            <header className="organization-report-page__header">
                <div>
                    <h1>{t('Rapport organisation')}</h1>
                    <p>{t('{{ name }} — du {{ from }} au {{ to }}', { name: data.organizationName, from: data.period.from, to: data.period.to })}</p>
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
                        {t('Télécharger en PDF')}
                    </Button>
                </div>
            </header>

            <DownloadReportModal
                isOpen={isDownloadOpen}
                onClose={() => setIsDownloadOpen(false)}
                report={data}
            />

            <section className="organization-report-page__section">
                <h2>{t('Patients & démographie')}</h2>
                <div className="organization-report-page__stats">
                    <ReportStatCard label={t('Patients total')} stat={data.demographics.totalPatients} />
                    <ReportStatCard label={t('Patients actifs')} stat={data.demographics.activePatients} />
                    <ReportStatCard label={t('Nouveaux patients')} stat={data.demographics.newPatients} />
                </div>
                <div className="organization-report-page__grid">
                    <Card><DistributionChart title={t('Répartition par genre')} items={data.demographics.genderDistribution} /></Card>
                    <Card><DistributionChart title={t("Tranches d'âge")} items={data.demographics.ageGroups} /></Card>
                </div>
            </section>

            <section className="organization-report-page__section">
                <h2>{t('État de santé')}</h2>
                <div className="organization-report-page__stats">
                    <ReportStatCard label={t('Glycémie moyenne')} stat={data.healthStatus.averageGlucose} />
                    <ReportStatCard label={t('Mesures glycémie')} stat={data.healthStatus.glucoseMeasurements} />
                    <ReportStatCard label={t('HbA1c moyenne')} stat={data.healthStatus.averageHbA1c} />
                    <ReportStatCard label={t('Tension systolique')} stat={data.healthStatus.averageSystolic} />
                    <ReportStatCard label={t('Tension diastolique')} stat={data.healthStatus.averageDiastolic} />
                    <ReportStatCard label={t('IMC moyen')} stat={data.healthStatus.averageBmi} />
                    <ReportStatCard label={t('Poids moyen')} stat={data.healthStatus.averageWeightKg} />
                </div>
                <Card>
                    <DistributionChart title={t('Répartition glycémique')} items={data.healthStatus.glucoseRanges} />
                </Card>
            </section>

            <section className="organization-report-page__section">
                <h2>{t('Activité médicale')}</h2>
                <div className="organization-report-page__stats">
                    <ReportStatCard label={t('Rendez-vous')} stat={data.medicalActivity.totalAppointments} />
                    <ReportStatCard label={t('Terminés')} stat={data.medicalActivity.completedAppointments} />
                    <ReportStatCard label={t('Annulés')} stat={data.medicalActivity.cancelledAppointments} />
                    <ReportStatCard label={t('Diagnostics')} stat={data.medicalActivity.diagnosesCount} />
                    <ReportStatCard label={t('Dossiers ouverts')} stat={data.medicalActivity.openMedicalRecords} />
                    <ReportStatCard label={t('Dossiers fermés')} stat={data.medicalActivity.closedMedicalRecords} />
                </div>
                <Card>
                    <DistributionChart title={t('Rendez-vous par statut')} items={data.medicalActivity.appointmentsByStatus} />
                </Card>
            </section>

            <section className="organization-report-page__section">
                <h2>{t('Traitements & observance')}</h2>
                <div className="organization-report-page__stats">
                    <ReportStatCard label={t('Prescriptions actives')} stat={data.treatments.activePrescriptions} />
                    <ReportStatCard label={t('Nouvelles prescriptions')} stat={data.treatments.newPrescriptions} />
                    <ReportStatCard label={t("Taux d'observance")} stat={data.treatments.adherenceRate} />
                    <ReportStatCard label={t('Prises enregistrées')} stat={data.treatments.totalIntakes} />
                </div>
                <Card>
                    <DistributionChart title={t('Prises par statut')} items={data.treatments.intakesByStatus} />
                </Card>
            </section>

            <section className="organization-report-page__section">
                <h2>{t('Nutrition & activité physique')}</h2>
                <div className="organization-report-page__stats">
                    <ReportStatCard label={t('Repas enregistrés')} stat={data.lifestyle.totalMeals} />
                    <ReportStatCard label={t("Séances d'activité")} stat={data.lifestyle.physicalActivitySessions} />
                    <ReportStatCard label={t("Minutes d'activité")} stat={data.lifestyle.totalActivityMinutes} />
                    <ReportStatCard label={t('Durée moyenne')} stat={data.lifestyle.averageActivityMinutes} />
                </div>
                <Card>
                    <DistributionChart title={t('Repas par type')} items={data.lifestyle.mealsByType} />
                </Card>
            </section>

            <section className="organization-report-page__section">
                <h2>{t('Tendances & indicateurs')}</h2>
                <div className="organization-report-page__stats">
                    <ReportStatCard label={t('Patients avec mesures')} stat={data.trends.patientsWithMeasurements} />
                    <ReportStatCard label={t('Taux de suivi')} stat={data.trends.measurementComplianceRate} />
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
