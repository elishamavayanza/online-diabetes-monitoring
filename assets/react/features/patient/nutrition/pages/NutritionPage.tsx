// NutritionPage.tsx
import { useState, useEffect } from 'react';
import { Tabs } from '@/react/components/Navigation/Tabs';
import { useNutrition } from '../hooks/useNutrition';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { fetchFoods } from '../services/nutritionService';
import { FoodOption, PatientMeal } from '../types';
import '@/styles/pages/patient/nutrition/_nutrition.scss';
import {FoodsTab} from "@/react/features/patient/nutrition/componenets/FoodsTab";
import {PlanTab} from "@/react/features/patient/nutrition/componenets/PlanTab";
import {PlanCreationModal} from "@/react/features/patient/nutrition/componenets/Forms/PlanCreationModal";
import {MealFormModal} from "@/react/features/patient/nutrition/componenets/Forms/MealFormModal";
import {MealItemFormModal} from "@/react/features/patient/nutrition/componenets/Forms/MealItemFormModal";

export function NutritionPage() {
    const {
        meals,
        mealItems,
        selectedFoods,
        setSelectedFoods,
        selectedDate,
        setSelectedDate,
        markedDates,
        isLoading,
        error,
        addMeal,
        removeMeal,
        addItem,
        removeItem,
        createPlan,
        categories,
    } = useNutrition();

    const [activeTab, setActiveTab] = useState<'foods' | 'plan'>('foods');
    const [isMealModalOpen, setIsMealModalOpen] = useState(false);
    const [isItemModalOpen, setIsItemModalOpen] = useState(false);
    const [isPlanModalOpen, setIsPlanModalOpen] = useState(false);
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

    // Type aligné avec PlanCreationModal
    const handleCreatePlan = async (data: {
        name: string;
        description?: string;
        mealType: string;
        items: { foodId: string; portionGrams: number; breadUnits?: number }[];
    }) => {
        setIsSubmitting(true);
        await createPlan(data);
        setIsSubmitting(false);
        setIsPlanModalOpen(false);
    };

    const handleAddItem = (meal: PatientMeal) => {
        setSelectedMeal(meal);
        setIsItemModalOpen(true);
    };

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    const tabs = [
        { id: 'foods', label: 'Aliments' },
        { id: 'plan', label: 'Mon plan' },
    ];

    return (
        <div className="nutrition-page">
            <div className="nutrition-page__header">
                <h1>Ma nutrition</h1>
                <Tabs
                    tabs={tabs}
                    defaultActiveTabId={activeTab}
                    onChange={(id) => setActiveTab(id as 'foods' | 'plan')}
                />
            </div>

            {activeTab === 'foods' ? (
                <FoodsTab
                    foods={foods}
                    categories={categories}   // ✅ prop passée
                    selectedFoods={selectedFoods}
                    setSelectedFoods={setSelectedFoods}
                    onCreatePlan={() => setIsPlanModalOpen(true)}
                />
            ) : (
                <PlanTab
                    meals={meals}
                    mealItems={mealItems}
                    foods={foods}
                    selectedDate={selectedDate}
                    setSelectedDate={setSelectedDate}
                    markedDates={markedDates}
                    onRemoveMeal={removeMeal}
                    onRemoveItem={removeItem}
                    onAddItem={handleAddItem}
                />
            )}

            <PlanCreationModal
                isOpen={isPlanModalOpen}
                onClose={() => setIsPlanModalOpen(false)}
                selectedFoods={selectedFoods}
                onSubmit={handleCreatePlan}
                isSubmitting={isSubmitting}
            />

            <MealFormModal
                isOpen={isMealModalOpen}
                onClose={() => setIsMealModalOpen(false)}
                onSuccess={async (data) => {
                    setIsSubmitting(true);
                    await addMeal(data);
                    setIsSubmitting(false);
                    setIsMealModalOpen(false);
                }}
                isSubmitting={isSubmitting}
            />

            <MealItemFormModal
                isOpen={isItemModalOpen}
                onClose={() => setIsItemModalOpen(false)}
                foods={foods}
                mealId={selectedMeal?.id || ''}
                onSuccess={async (data) => {
                    if (!selectedMeal) return;
                    setIsSubmitting(true);
                    await addItem({ ...data, mealId: selectedMeal.id });
                    setIsSubmitting(false);
                    setIsItemModalOpen(false);
                    setSelectedMeal(null);
                }}
                isSubmitting={isSubmitting}
            />
        </div>
    );
}
