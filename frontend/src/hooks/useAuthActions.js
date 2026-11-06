// Login dan logout admin (logika ada di services/authService.js).
import { useMutation } from '@tanstack/react-query';
import { authService } from '../services';

export const useLogin = () => useMutation({ mutationFn: authService.login });

export const useLogout = () => useMutation({ mutationFn: authService.logout });
