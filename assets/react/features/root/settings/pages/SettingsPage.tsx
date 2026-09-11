import { useEffect, useState } from 'react';
import { useSearchParams } from 'react-router-dom';
import { useSettings } from '../hooks/useSettings';
import { GeneralSettings } from '../components/GeneralSettings';
import { HomeSettings } from '../components/HomeSettings';
import { invalidateSystemSettings } from '@/react/hooks/useSystemSettings';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { Modal } from '@/react/components/UI/Modal';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import { useI18n } from '@/react/i18n/I18nContext';
import { SettingsData } from '../types';
import '@/styles/pages/root/settings/_settings.scss';

type SettingsTab = 'general' | 'home';

export function SettingsPage() {
    const { settings, isLoading, isSaving, error, save } = useSettings();
    const [searchParams] = useSearchParams();
    const [draft, setDraft] = useState<SettingsData | null>(null);
    const [logoFile, setLogoFile] = useState<File | null>(null);
    const [activeTab, setActiveTab] = useState<SettingsTab>(
        searchParams.get('tab') === 'home' ? 'home' : 'general'
    );
    const [modalOpen, setModalOpen] = useState(false);
    const { pushAction } = useActionHistory();
    const { t } = useI18n();

    useEffect(() => {
        const tab = searchParams.get('tab');
        if (tab === 'general' || tab === 'home') {
            setActiveTab(tab);
        }
    }, [searchParams]);

    useEffect(() => {
        if (settings) {
            setDraft(settings);
            setLogoFile(null);
        }
    }, [settings]);

    const openHelp = () => {
        setModalOpen(true);
        pushAction(() => setModalOpen(false));
    };

    if (isLoading || !draft) {
        return <Spinner />;
    }

    if (error && !draft) {
        return <Alert variant="error">{error ?? t('Paramètres indisponibles.')}</Alert>;
    }

    const handleChange = (patch: Partial<SettingsData>) => {
        setDraft((prev) => (prev ? { ...prev, ...patch } : prev));
    };

    const handleSave = async () => {
        const ok = await save(draft, logoFile);
        if (ok) {
            setLogoFile(null);
            invalidateSystemSettings();
        }
    };

    return (
        <div className="settings-page">
            <div className="settings-page__header">
                <h1>{t('Configuration système')}</h1>
                <p>{t('Gérez les paramètres globaux de la plateforme')}</p>
                <Button variant="secondary" onClick={openHelp}>{t('Aide')}</Button>
            </div>

            {error && <Alert variant="error">{error}</Alert>}

            <div className="settings-tabs" role="tablist">
                <button
                    type="button"
                    role="tab"
                    aria-selected={activeTab === 'general'}
                    className={`settings-tabs__tab${activeTab === 'general' ? ' settings-tabs__tab--active' : ''}`}
                    onClick={() => setActiveTab('general')}
                >
                    {t('Identité')}
                </button>
                <button
                    type="button"
                    role="tab"
                    aria-selected={activeTab === 'home'}
                    className={`settings-tabs__tab${activeTab === 'home' ? ' settings-tabs__tab--active' : ''}`}
                    onClick={() => setActiveTab('home')}
                >
                    {t('Page d\u2019accueil')}
                </button>
            </div>

            {activeTab === 'general' ? (
                <GeneralSettings
                    settings={draft}
                    logoFile={logoFile}
                    onChange={handleChange}
                    onLogoChange={setLogoFile}
                    onSave={() => void handleSave()}
                    isSaving={isSaving}
                />
            ) : (
                <HomeSettings
                    settings={draft}
                    onChange={handleChange}
                    onSave={() => void handleSave()}
                    isSaving={isSaving}
                />
            )}

            {modalOpen && (
                <Modal isOpen={modalOpen} onClose={() => setModalOpen(false)}>
                    <p>
                        {t('La configuration système permet au super administrateur de personnaliser l\u2019identité de la plateforme (nom, logo) et les textes affichés sur la page d\u2019accueil publique.')}
                    </p>
                </Modal>
            )}
        </div>
    );
}