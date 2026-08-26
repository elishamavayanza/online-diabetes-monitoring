export interface AddressData {
    street: string;
    city: string;
    postalCode: string;
    country: string;
}

export interface EstablishmentFormValues {
    organizationId: string;
    name: string;
    phone: string;
    address: AddressData;
}

export interface Establishment extends EstablishmentFormValues {
    id: string;
}
