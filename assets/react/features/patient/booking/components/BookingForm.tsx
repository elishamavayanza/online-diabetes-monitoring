import { Card } from '@/react/components/UI/Card';
import { FormField } from '@/react/components/Forms/FormField';
import { Select } from '@/react/components/Forms/Select';
import { Input } from '@/react/components/Forms/Input';
import { Button } from '@/react/components/UI/Button';
import { Alert } from '@/react/components/UI/Alert';
import { Spinner } from '@/react/components/UI/Spinner';
import { useBooking } from '../hooks/useBooking';

export function BookingForm() {
    const {
        professionals,
        slots,
        form,
        updateForm,
        submit,
        isLoading,
        isSubmitting,
        error,
        success,
    } = useBooking();

    if (isLoading) return <Spinner />;
    if (success) return <Alert variant="success">Rendez-vous confirmé !</Alert>;

    const professionalOptions = professionals.map((p) => ({
        value: p.id,
        label: `${p.nom} - ${p.specialite}`,
    }));

    const slotOptions = slots
        .filter((s) => s.disponible)
        .map((s) => ({ value: s.time, label: s.time }));

    return (
        <Card className="booking-card">
            <h2>Prendre rendez-vous</h2>
            {error && <Alert variant="error">{error}</Alert>}
            <form
                onSubmit={(e) => {
                    e.preventDefault();
                    submit();
                }}
            >
                <FormField label="Professionnel">
                    <Select
                        value={form.professionnelId}
                        onChange={(e) => updateForm('professionnelId', e.target.value)}
                        options={professionalOptions}
                        placeholder="Choisir un professionnel"
                    />
                </FormField>
                <FormField label="Date">
                    <Input
                        type="date"
                        value={form.date}
                        onChange={(e) => updateForm('date', e.target.value)}
                    />
                </FormField>
                <FormField label="Heure">
                    <Select
                        value={form.heure}
                        onChange={(e) => updateForm('heure', e.target.value)}
                        options={slotOptions}
                        placeholder="Choisir une heure"
                    />
                </FormField>
                <FormField label="Motif">
                    <Input
                        value={form.motif}
                        onChange={(e) => updateForm('motif', e.target.value)}
                        placeholder="Motif de la consultation"
                    />
                </FormField>
                <Button type="submit" disabled={isSubmitting}>
                    {isSubmitting ? 'Confirmation...' : 'Confirmer'}
                </Button>
            </form>
        </Card>
    );
}
