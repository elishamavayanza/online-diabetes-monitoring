import React from 'react';
import { Card } from '@/react/components/UI/Card';
import { Form } from '@/react/components/Forms/Form';
import { FormField } from '@/react/components/Forms/FormField';
import { Input } from '@/react/components/Forms/Input';
import { Textarea } from '@/react/components/Forms/Textarea';
import { Button } from '@/react/components/UI/Button';
import { useI18n } from '@/react/i18n/I18nContext';
import { SettingsData, SettingsItem } from '../types';

interface HomeSettingsProps {
    settings: SettingsData;
    onChange: (patch: Partial<SettingsData>) => void;
    onSave: () => void;
    isSaving: boolean;
}

export function HomeSettings({ settings, onChange, onSave, isSaving }: HomeSettingsProps) {
    const { t } = useI18n();
    return (
        <Card className="settings-card">
            <h2>{t('Page d\u2019accueil')}</h2>
            <Form
                onSubmit={(e: React.FormEvent) => {
                    e.preventDefault();
                    onSave();
                }}
            >
                <FormField label={t('Titre (héro)')}>
                    <Textarea
                        rows={2}
                        value={settings.heroTitle}
                        onChange={(e: React.ChangeEvent<HTMLTextAreaElement>) =>
                            onChange({ heroTitle: e.target.value })
                        }
                    />
                </FormField>

                <FormField label={t('Sous-titre (héro)')}>
                    <Textarea
                        rows={3}
                        value={settings.heroSubtitle}
                        onChange={(e: React.ChangeEvent<HTMLTextAreaElement>) =>
                            onChange({ heroSubtitle: e.target.value })
                        }
                    />
                </FormField>

                <FormField label={t('Titre (À propos)')}>
                    <Input
                        value={settings.aboutTitle}
                        onChange={(e: React.ChangeEvent<HTMLInputElement>) =>
                            onChange({ aboutTitle: e.target.value })
                        }
                    />
                </FormField>

                <FormField label={t('Contenu (À propos)')}>
                    <Textarea
                        rows={4}
                        value={settings.aboutContent}
                        onChange={(e: React.ChangeEvent<HTMLTextAreaElement>) =>
                            onChange({ aboutContent: e.target.value })
                        }
                    />
                </FormField>

                <FormField label={t('Titre (Fonctionnalités)')}>
                    <Input
                        value={settings.featuresTitle}
                        onChange={(e: React.ChangeEvent<HTMLInputElement>) =>
                            onChange({ featuresTitle: e.target.value })
                        }
                    />
                </FormField>

                <FormField label={t('Fonctionnalités')}>
                    <ItemsEditor
                        items={settings.features}
                        onChange={(features) => onChange({ features })}
                    />
                </FormField>

                <FormField label={t('Titre (Pour qui ?)')}>
                    <Input
                        value={settings.usersTitle}
                        onChange={(e: React.ChangeEvent<HTMLInputElement>) =>
                            onChange({ usersTitle: e.target.value })
                        }
                    />
                </FormField>

                <FormField label={t('Publics cibles')}>
                    <ItemsEditor
                        items={settings.users}
                        onChange={(users) => onChange({ users })}
                    />
                </FormField>

                <FormField label={t('Titre (appel à l\u2019action)')}>
                    <Textarea
                        rows={2}
                        value={settings.ctaTitle}
                        onChange={(e: React.ChangeEvent<HTMLTextAreaElement>) =>
                            onChange({ ctaTitle: e.target.value })
                        }
                    />
                </FormField>

                <FormField label={t('Sous-titre (appel à l\u2019action)')}>
                    <Textarea
                        rows={2}
                        value={settings.ctaSubtitle}
                        onChange={(e: React.ChangeEvent<HTMLTextAreaElement>) =>
                            onChange({ ctaSubtitle: e.target.value })
                        }
                    />
                </FormField>

                <FormField label={t('Slogan du pied de page')}>
                    <Textarea
                        rows={2}
                        value={settings.footerTagline}
                        onChange={(e: React.ChangeEvent<HTMLTextAreaElement>) =>
                            onChange({ footerTagline: e.target.value })
                        }
                    />
                </FormField>

                <FormField label={t('Copyright')}>
                    <Input
                        value={settings.footerCopyright}
                        onChange={(e: React.ChangeEvent<HTMLInputElement>) =>
                            onChange({ footerCopyright: e.target.value })
                        }
                    />
                </FormField>

                <Button type="submit" disabled={isSaving}>
                    {isSaving ? t('Enregistrement...') : t('Enregistrer')}
                </Button>
            </Form>
        </Card>
    );
}

const AddIcon = () => (
    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" strokeWidth="2">
        <line x1="12" y1="5" x2="12" y2="19" />
        <line x1="5" y1="12" x2="19" y2="12" />
    </svg>
);

const TrashIcon = () => (
    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" strokeWidth="2">
        <polyline points="3 6 5 6 21 6" />
        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
    </svg>
);

interface ItemsEditorProps {
    items: SettingsItem[];
    onChange: (items: SettingsItem[]) => void;
}

function ItemsEditor({ items, onChange }: ItemsEditorProps) {
    const { t } = useI18n();
    const update = (index: number, patch: Partial<SettingsItem>) => {
        const next = items.map((item, i) => (i === index ? { ...item, ...patch } : item));
        onChange(next);
    };

    const add = () => {
        onChange([...items, { title: '', description: '' }]);
    };

    const remove = (index: number) => {
        onChange(items.filter((_, i) => i !== index));
    };

    return (
        <div className="settings-items">
            {items.map((item, index) => (
                <div className="settings-items__row" key={index}>
                    <Input
                        placeholder={t('Titre')}
                        value={item.title}
                        onChange={(e: React.ChangeEvent<HTMLInputElement>) =>
                            update(index, { title: e.target.value })
                        }
                    />
                    <Input
                        placeholder={t('Description')}
                        value={item.description}
                        onChange={(e: React.ChangeEvent<HTMLInputElement>) =>
                            update(index, { description: e.target.value })
                        }
                    />
                    <button
                        type="button"
                        className="settings-items__remove"
                        onClick={() => remove(index)}
                        aria-label={t('Supprimer l\u2019élément')}
                        title={t('Supprimer')}
                    >
                        <TrashIcon />
                    </button>
                </div>
            ))}
            <button type="button" className="settings-items__add" onClick={add}>
                <AddIcon />
                {t('Ajouter un élément')}
            </button>
        </div>
    );
}