import { ChangeEvent, useState } from 'react';

const useFormInput = (initialValue: number | string) => {
    const [value, setValue] = useState(initialValue);

    const onChange = (ev: ChangeEvent): void => {
        setValue((ev.target as HTMLInputElement).value);
    };

    return {
        value,
        setValue,
        onChange,
    };
};

export default useFormInput;
