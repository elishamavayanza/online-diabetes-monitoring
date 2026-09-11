import { useI18n } from '@/react/i18n/I18nContext';
import { getRecordCreatorName, RecordOwnershipInfo } from '../utils/ownershipUtils';

interface RecordAuthorProps {
    record: RecordOwnershipInfo;
    label?: string;
    className?: string;
}

export function RecordAuthor({
    record,
    label = 'Créé par',
    className = 'record-author',
}: RecordAuthorProps) {
    const { t } = useI18n();
    const name = getRecordCreatorName(record);
    if (!name) {
        return null;
    }
    return (
        <span className={className}>
            {t(label)} {name}
        </span>
    );
}