interface AlertProps {
    message: string;
}

const Alert: React.FC<AlertProps> = ({ message }) => {
    return (
        <p
            className="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400"
            role="alert"
        >
            {message}
        </p>
    );
};

export default Alert;
