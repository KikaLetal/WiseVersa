export const isEmoji = (str: string | undefined): boolean => {
        if (!str) return false;
        if (str.includes('/') || str.includes('\\')) return false;
        if (/\.(svg|png|jpg|jpeg|gif|webp)$/i.test(str)) return false;
        return true;    
    };