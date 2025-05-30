import { Card } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem, type DashboardData } from '@/types';
import { Head } from '@inertiajs/react';
import { Bar, BarChart, ResponsiveContainer, Tooltip, XAxis } from 'recharts';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

interface DashboardProps {
    postsOverTime: DashboardData['postsOverTime'];
    commentsOverTime: DashboardData['commentsOverTime'];
    totalPosts: DashboardData['totalPosts'];
    totalComments: DashboardData['totalComments'];
}

export default function Dashboard({ postsOverTime, commentsOverTime, totalPosts, totalComments }: DashboardProps) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div className="grid auto-rows-min gap-4 md:grid-cols-2">
                    <Card className="p-6">
                        <div className="mb-4 flex items-center justify-between">
                            <h3 className="text-lg font-semibold">Posts</h3>
                            <div className="text-primary text-2xl font-bold">{totalPosts}</div>
                        </div>
                        <div className="h-[200px]">
                            <ResponsiveContainer width="100%" height="100%">
                                <BarChart data={postsOverTime}>
                                    <XAxis dataKey="date" fontSize={12} axisLine={false} tickLine={false} />
                                    <Tooltip />
                                    <Bar dataKey="count" fill="var(--primary)" />
                                </BarChart>
                            </ResponsiveContainer>
                        </div>
                        <p className="text-muted-foreground mt-2 text-sm">Posts created over the last 30 days</p>
                    </Card>

                    <Card className="p-6">
                        <div className="mb-4 flex items-center justify-between">
                            <h3 className="text-lg font-semibold">Comments</h3>
                            <div className="text-primary text-2xl font-bold">{totalComments}</div>
                        </div>
                        <div className="h-[200px]">
                            <ResponsiveContainer width="100%" height="100%">
                                <BarChart data={commentsOverTime}>
                                    <XAxis dataKey="date" fontSize={12} axisLine={false} tickLine={false} />
                                    <Tooltip />
                                    <Bar dataKey="count" fill="var(--primary)" />
                                </BarChart>
                            </ResponsiveContainer>
                        </div>
                        <p className="text-muted-foreground mt-2 text-sm">Comments created over the last 30 days</p>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}
