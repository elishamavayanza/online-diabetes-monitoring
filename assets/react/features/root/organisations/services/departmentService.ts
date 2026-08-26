import { DepartmentFormValues } from '../types/department';

export async function createDepartment(payload: DepartmentFormValues): Promise<void> {
    await new Promise((resolve) => setTimeout(resolve, 800));
    console.log('Département créé', payload);
    // Appel API réel à implémenter
}

export async function updateDepartment(id: string, payload: DepartmentFormValues): Promise<void> {
    await new Promise((resolve) => setTimeout(resolve, 800));
    console.log('Département mis à jour', id, payload);
    // Appel API réel à implémenter
}
