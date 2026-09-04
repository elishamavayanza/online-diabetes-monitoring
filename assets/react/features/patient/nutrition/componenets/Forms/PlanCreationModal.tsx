import { useState } from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Button } from '@/react/components/UI/Button';
import { FormField } from '@/react/components/Forms/FormField';
import { Select } from '@/react/components/Forms/Select';
import { Input } from '@/react/components/Forms/Input';
import { FoodOption } from '../../types';

interface PlanCreationModalProps {
    isOpen: boolean;
    onClose: () => void;
    selectedFoods: FoodOption[];
    onSubmit: (data: {
        name: string;
        description?: string;
        mealType: string;
        items: {
            foodId: string;
            portionGrams: number;
            breadUnits?: number;
        }[];
    }) => void;
    isSubmitting: boolean;
}

const MEAL_TYPE_OPTIONS = [
    { value: 'BREAKFAST', label: 'Petit-déjeuner' },
    { value: 'LUNCH', label: 'Déjeuner' },
    { value: 'DINNER', label: 'Dîner' },
    { value: 'SNACK', label: 'Collation' },
    { value: 'OTHER', label: 'Autre (personnalisé)' },
];

const UNIT_OPTIONS = [
    { value: 'g', label: 'g' },
    { value: 'ml', label: 'ml' },
    { value: 'portion', label: 'portion' },
    { value: 'unité', label: 'unité' },
];

const UNIT_TO_GRAMS: Record<string, number> = {
    g: 1,
    ml: 1,
    portion: 100,
    'unité': 80,
};

export function PlanCreationModal({
                                      isOpen,
                                      onClose,
                                      selectedFoods,
                                      onSubmit,
                                      isSubmitting,
                                  }: PlanCreationModalProps) {
    const [mealType, setMealType] = useState<string>('LUNCH');
    const [customMealType, setCustomMealType] = useState('');
    const [name, setName] = useState('');
    const [description, setDescription] = useState('');
    const [quantities, setQuantities] = useState<Record<string, string>>({});
    const [breadUnitsMap, setBreadUnitsMap] = useState<Record<string, string>>({});
    const [units, setUnits] = useState<Record<string, string>>({});

    const isCustomType = mealType === 'OTHER';

    const handleQuantityChange = (foodId: string, value: string) => {
        setQuantities((prev) => ({ ...prev, [foodId]: value }));
    };

    const handleBreadUnitsChange = (foodId: string, value: string) => {
        setBreadUnitsMap((prev) => ({ ...prev, [foodId]: value }));
    };

    const handleUnitChange = (foodId: string, value: string) => {
        setUnits((prev) => ({ ...prev, [foodId]: value }));
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        const finalMealType = isCustomType ? customMealType.trim() : mealType;
        if (!finalMealType) return;

        const items = selectedFoods.map((food) => {
            const unit = units[food.id] || 'g';
            const rawQuantity = parseFloat(quantities[food.id] || '100');
            const factor = UNIT_TO_GRAMS[unit] || 1;
            const portionGrams = rawQuantity * factor;

            return {
                foodId: food.id,
                portionGrams,
                breadUnits: breadUnitsMap[food.id]
                    ? parseFloat(breadUnitsMap[food.id])
                    : undefined,
            };
        });

        onSubmit({
            name: name.trim() || `Repas ${finalMealType}`,
            description: description.trim() || undefined,
            mealType: finalMealType,
            items,
        });

        setName('');
        setDescription('');
        setMealType('LUNCH');
        setCustomMealType('');
        setQuantities({});
        setBreadUnitsMap({});
        setUnits({});
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose} title="Créer un plan repas">
            <form onSubmit={handleSubmit} className="dossier-form">
                <FormField label="Nom du repas">
                    <Input
                        value={name}
                        onChange={(e) => setName(e.target.value)}
                        placeholder="Ex: Déjeuner du lundi"
                    />
                </FormField>

                <FormField label="Description">
                    <Input
                        value={description}
                        onChange={(e) => setDescription(e.target.value)}
                        placeholder="Optionnel"
                    />
                </FormField>

                <FormField label="Type de repas *">
                    <Select
                        value={mealType}
                        onChange={(e) => setMealType(e.target.value)}
                        options={MEAL_TYPE_OPTIONS}
                        required
                    />
                </FormField>

                {isCustomType && (
                    <FormField label="Type personnalisé *">
                        <Input
                            value={customMealType}
                            onChange={(e) => setCustomMealType(e.target.value)}
                            placeholder="Ex: Brunch, Collation soir"
                            required
                        />
                    </FormField>
                )}

                <div className="plan-food-list">
                    {selectedFoods.map((food) => (
                        <div key={food.id} className="plan-food-item">
                            <span className="plan-food-item__name">{food.name}</span>

                            {/* Champ 1 : Quantité + Unité regroupées */}
                            <div className="quantity-unit-group">
                                <Input
                                    type="number"
                                    min="1"
                                    step="1"
                                    value={quantities[food.id] || '100'}
                                    onChange={(e) => handleQuantityChange(food.id, e.target.value)}
                                    placeholder="Quantité"
                                    required
                                />
                                <Select
                                    value={units[food.id] || 'g'}
                                    onChange={(e) => handleUnitChange(food.id, e.target.value)}
                                    options={UNIT_OPTIONS}
                                    required
                                />
                            </div>

                            {/* Champ 2 : Unités pain */}
                            <Input
                                type="number"
                                min="0"
                                step="0.1"
                                value={breadUnitsMap[food.id] || ''}
                                onChange={(e) => handleBreadUnitsChange(food.id, e.target.value)}
                                placeholder="Unités pain"
                            />
                        </div>
                    ))}
                </div>

                <div className="dossier-form__actions">
                    <Button type="button" variant="secondary" onClick={onClose}>Annuler</Button>
                    <Button type="submit" variant="primary" disabled={isSubmitting}>
                        {isSubmitting ? 'Création...' : 'Créer le plan'}
                    </Button>
                </div>
            </form>
        </Modal>
    );
}
