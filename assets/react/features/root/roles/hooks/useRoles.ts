import { useEffect, useState } from 'react';
import { fetchRolesData } from '../services/rolesService';
import { RoleData, RoleId } from '../types';

export function useRoles() {
    const [data, setData] = useState<RoleData | null>(null);
    const [selectedRoleId, setSelectedRoleId] = useState<RoleId>('ROOT');
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const load = async () => {
            try {
                const result = await fetchRolesData();
                setData(result);
            } catch (err) {
                setError('Impossible de charger les rôles.');
            } finally {
                setIsLoading(false);
            }
        };
        load();
    }, []);

    const selectedRole = data?.roles.find((r) => r.id === selectedRoleId) ?? null;
    const users = data?.usersByRole[selectedRoleId] ?? [];

    return {
        roles: data?.roles ?? [],
        selectedRole,
        selectedRoleId,
        setSelectedRoleId,
        users,
        isLoading,
        error,
    };
}
