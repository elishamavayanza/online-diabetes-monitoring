import { useState, useEffect } from 'react';
import { useNutrition } from '../hooks/useNutrition';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { Card } from '@/react/components/UI/Card';
import { Badge } from '@/react/components/UI/Badge';
import { fetchFoods } from '../services/nutritionService';
import { FoodOption, PatientMeal, MealType } from '../types';
import '@/styles/pages/patient/nutrition/_nutrition.scss';
import {MealItemFormModal} from "@/react/features/patient/nutrition/componenets/MealItemFormModal";
import {MealFormModal} from "@/react/features/patient/nutrition/componenets/MealFormModal";

// Types pour les données des formulaires
interface MealFormData {
    name: string;
    description?: string;
    mealType: MealType;
}

interface MealItemFormData {
    foodId: string;
    portionGrams: string;
    breadUnits?: string;
}

export function NutritionPage() {
    const { meals, mealItems, isLoading, error, addMeal, removeMeal, addItem, removeItem } = useNutrition();
    const [isMealModalOpen, setIsMealModalOpen] = useState(false);
    const [isItemModalOpen, setIsItemModalOpen] = useState(false);
    const [foods, setFoods] = useState<FoodOption[]>([]);
    const [selectedMeal, setSelectedMeal] = useState<PatientMeal | null>(null);
    const [isSubmitting, setIsSubmitting] = useState(false);

    useEffect(() => {
        const loadFoods = async () => {
            try {
                const data = await fetchFoods();
                setFoods(data);
            } catch (e) {
                console.error('Erreur de chargement des aliments', e);
            }
        };
        loadFoods();
    }, []);

    // ✅ Typage du paramètre data
    const handleAddMeal = async (data: MealFormData) => {
        setIsSubmitting(true);
        await addMeal(data);
        setIsSubmitting(false);
        setIsMealModalOpen(false);
    };

    // ✅ Typage du paramètre data
    const handleAddItem = async (data: MealItemFormData) => {
        if (!selectedMeal) return;
        setIsSubmitting(true);
        await addItem({ ...data, mealId: selectedMeal.id });
        setIsSubmitting(false);
        setIsItemModalOpen(false);
        setSelectedMeal(null);
    };

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    return (
        <div className="nutrition-page">
            <div className="nutrition-page__header">
                <h1>Ma nutrition</h1>
                <p>Suivi de vos repas</p>
                <Button variant="primary" onClick={() => setIsMealModalOpen(true)}>+ Nouveau repas</Button>
            </div>

            {meals.length === 0 ? (
                <Card><p>Aucun repas enregistré.</p></Card>
            ) : (
                <div className="nutrition-page__meals">
                    {meals.map((meal) => (
                        <Card key={meal.id} className="meal-card">
                            <div className="meal-card__header">
                                <h3>{meal.name}</h3>
                                <Badge variant="info">{meal.mealType}</Badge>
                                <Button variant="danger" size="small" onClick={() => removeMeal(meal.id)}>Supprimer</Button>
                            </div>
                            {meal.description && <p className="meal-card__desc">{meal.description}</p>}
                            <div className="meal-card__items">
                                {mealItems[meal.id]?.length ? (
                                    mealItems[meal.id].map((item) => (
                                        <div key={item.id} className="meal-item">
                                            <span>{item.foodName || 'Aliment'}</span>
                                            <span>{item.portionGrams} g</span>
                                            {item.breadUnits && <span>{item.breadUnits} UP</span>}
                                            <Button variant="secondary" size="small" onClick={() => removeItem(item.id)}>Retirer</Button>
                                        </div>
                                    ))
                                ) : (
                                    <p>Aucun aliment dans ce repas.</p>
                                )}
                            </div>
                            <Button variant="secondary" size="small" onClick={() => { setSelectedMeal(meal); setIsItemModalOpen(true); }}>+ Aliment</Button>
                        </Card>
                    ))}
                </div>
            )}

            <MealFormModal
                isOpen={isMealModalOpen}
                onClose={() => setIsMealModalOpen(false)}
                onSuccess={handleAddMeal}
                isSubmitting={isSubmitting}
            />

            <MealItemFormModal
                isOpen={isItemModalOpen}
                onClose={() => setIsItemModalOpen(false)}
                foods={foods}
                mealId={selectedMeal?.id || ''}
                onSuccess={handleAddItem}
                isSubmitting={isSubmitting}
            />
        </div>
    );
}
