export type UserRole = 'admin' | 'supervisor' | 'student';

export interface User {
  id: string;
  name: string;
  role: UserRole;
  staffId?: string;
  indexNumber?: string;
}

export interface AdminDashboardStats {
  registeredStudents: number;
  orientationChecklists: number;
  elogbooksSubmitted: number;
  contractsPending: number;
  contractsApproved: number;
  reportsSubmitted: number;
  assumptionsCount: number;
  visitingScoresCount: number;
  companyScoresCount: number;
}

export interface SupervisorDashboardStats {
  totalStudents: number;
  firstVisits: number;
  secondVisits: number;
  firstVisitWithScoresheet: number;
  secondVisitWithScoresheet: number;
}

export interface StudentSummary {
  student_index: string;
  first_name: string;
  last_name: string;
  company_name: string;
  company_region: string;
  attachment_region: string;
  visit_number: number;
}

export interface ChartDataPoint {
  name: string;
  value: number;
  [key: string]: string | number;
}

export interface FacultyDistribution {
  faculty: string;
  count: number;
}

export interface RegionDistribution {
  region: string;
  count: number;
}
