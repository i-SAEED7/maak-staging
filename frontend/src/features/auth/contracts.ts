export type AuthFormState = {
  identifier: string;
  password: string;
};

export type ForgotPasswordState = {
  identifier: string;
  otp?: string;
  password?: string;
  password_confirmation?: string;
};
