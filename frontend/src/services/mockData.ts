import type {
  AdminDashboardStats,
  SupervisorDashboardStats,
  StudentSummary,
  ChartDataPoint,
  FacultyDistribution,
  RegionDistribution,
} from '@/types';

export const mockAdminStats: AdminDashboardStats = {
  registeredStudents: 248,
  orientationChecklists: 192,
  elogbooksSubmitted: 210,
  contractsPending: 18,
  contractsApproved: 195,
  reportsSubmitted: 165,
  assumptionsCount: 230,
  visitingScoresCount: 140,
  companyScoresCount: 155,
};

export const mockSupervisorStats: SupervisorDashboardStats = {
  totalStudents: 24,
  firstVisits: 24,
  secondVisits: 12,
  firstVisitWithScoresheet: 18,
  secondVisitWithScoresheet: 8,
};

export const mockStudents: StudentSummary[] = [
  {
    student_index: '04/2014/0001D',
    first_name: 'John',
    last_name: 'Doe',
    company_name: 'Tech Solutions Ltd',
    company_region: 'Harare',
    attachment_region: 'Harare',
    visit_number: 1,
  },
  {
    student_index: '04/2014/0002D',
    first_name: 'Jane',
    last_name: 'Smith',
    company_name: 'Health Plus',
    company_region: 'Bulawayo',
    attachment_region: 'Bulawayo',
    visit_number: 1,
  },
];

export const mockFacultyDistribution: FacultyDistribution[] = [
  { faculty: 'COM', count: 45 },
  { faculty: 'ENG', count: 38 },
  { faculty: 'SCI', count: 32 },
  { faculty: 'EDU', count: 28 },
  { faculty: 'AGR', count: 22 },
  { faculty: 'MED', count: 20 },
  { faculty: 'ARTS', count: 18 },
  { faculty: 'SOC', count: 15 },
  { faculty: 'LAW', count: 12 },
  { faculty: 'CIE', count: 10 },
  { faculty: 'VET', count: 4 },
];

export const mockRegionDistribution: RegionDistribution[] = [
  { region: 'Harare', count: 85 },
  { region: 'Bulawayo', count: 42 },
  { region: 'Manicaland', count: 28 },
  { region: 'Midlands', count: 25 },
  { region: 'Mashonaland East', count: 22 },
  { region: 'Masvingo', count: 18 },
  { region: 'Mashonaland West', count: 14 },
  { region: 'Matabeleland North', count: 8 },
  { region: 'Mashonaland Central', count: 4 },
  { region: 'Matabeleland South', count: 2 },
];

export const mockRegistrationsByMonth: ChartDataPoint[] = [
  { name: 'Jan', value: 12 },
  { name: 'Feb', value: 28 },
  { name: 'Mar', value: 45 },
  { name: 'Apr', value: 62 },
  { name: 'May', value: 58 },
  { name: 'Jun', value: 43 },
];

export const mockSubmissionsTrend: ChartDataPoint[] = [
  { name: 'Week 1', value: 20 },
  { name: 'Week 2', value: 45 },
  { name: 'Week 3', value: 78 },
  { name: 'Week 4', value: 112 },
  { name: 'Week 5', value: 145 },
  { name: 'Week 6', value: 180 },
];

export const mockVisitScoresData: ChartDataPoint[] = [
  { name: 'First visit', value: 18 },
  { name: 'Second visit', value: 8 },
];
