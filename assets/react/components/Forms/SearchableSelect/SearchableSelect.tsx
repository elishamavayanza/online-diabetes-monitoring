import React, { useState, useRef, useEffect } from 'react';
import '../../../../styles/components/Form/SearchableSelect.scss';

export interface SearchableSelectOption {
    value: string | number;
    label: string;
}

export interface SearchableSelectProps {
    options: SearchableSelectOption[];
    value: string | number | '';
    onChange: (value: string) => void;
    placeholder?: string;
    disabled?: boolean;
    required?: boolean;
    className?: string;
}

export function SearchableSelect({
                                     options,
                                     value,
                                     onChange,
                                     placeholder = 'Rechercher...',
                                     disabled = false,
                                     required = false,
                                     className,
                                 }: SearchableSelectProps) {
    const [isOpen, setIsOpen] = useState(false);
    const [search, setSearch] = useState('');
    const wrapperRef = useRef<HTMLDivElement>(null);

    // Fermer le dropdown si clic à l'extérieur
    useEffect(() => {
        function handleClickOutside(e: MouseEvent) {
            if (wrapperRef.current && !wrapperRef.current.contains(e.target as Node)) {
                setIsOpen(false);
            }
        }
        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    // Filtrer les options selon la recherche
    const filteredOptions = options.filter((opt) =>
        opt.label.toLowerCase().includes(search.toLowerCase())
    );

    const handleSelect = (val: string) => {
        onChange(val);
        setIsOpen(false);
        setSearch('');
    };

    return (
        <div className={`searchable-select ${className ?? ''}`} ref={wrapperRef}>
            <input
                type="text"
                className="searchable-select__input"
                placeholder={placeholder}
                value={search}
                onClick={() => setIsOpen((prev) => !prev)}
                onChange={(e) => setSearch(e.target.value)}
                disabled={disabled}
                required={required}
            />
            {isOpen && (
                <div className="searchable-select__dropdown">
                    {filteredOptions.length > 0 ? (
                        filteredOptions.map((opt) => (
                            <div
                                key={opt.value}
                                className={`searchable-select__option ${
                                    String(opt.value) === String(value)
                                        ? 'searchable-select__option--selected'
                                        : ''
                                }`}
                                onClick={() => handleSelect(String(opt.value))}
                            >
                                {opt.label}
                            </div>
                        ))
                    ) : (
                        <div className="searchable-select__empty">Aucun résultat</div>
                    )}
                </div>
            )}
        </div>
    );
}
