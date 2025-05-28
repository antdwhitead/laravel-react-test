import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { Plus, User, MessageCircle, Clock } from 'lucide-react';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Posts',
        href: '/posts',
    },
];

interface Post {
    id: number;
    name: string;
    content: string;
    created_at: string;
    updated_at: string;
    user: {
        id: number;
        name: string;
        email: string;
    };
    comments: Array<{
        id: number;
        content: string;
        user: {
            id: number;
            name: string;
        };
    }>;
}

interface PostsPageProps {
    posts: {
        data: Post[];
        current_page: number;
        per_page: number;
        total: number;
        last_page: number;
        links: Array<{
            url: string | null;
            label: string;
            active: boolean;
        }>;
    };
}

export default function PostsIndex({ posts }: PostsPageProps) {
    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Posts" />
            
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-semibold tracking-tight">Posts</h1>
                        <p className="text-muted-foreground">Manage and view all blog posts</p>
                    </div>
                    <Link href="/posts/create">
                        <Button>
                            <Plus className="h-4 w-4" />
                            Create Post
                        </Button>
                    </Link>
                </div>

                {posts.data.length === 0 ? (
                    <Card className="text-center py-12">
                        <CardContent>
                            <div className="flex flex-col items-center gap-4">
                                <div className="h-12 w-12 rounded-full bg-muted flex items-center justify-center">
                                    <Plus className="h-6 w-6 text-muted-foreground" />
                                </div>
                                <div>
                                    <h3 className="text-lg font-semibold">No posts yet</h3>
                                    <p className="text-muted-foreground text-sm">Create your first blog post to get started.</p>
                                </div>
                                <Link href="/posts/create">
                                    <Button>Create Post</Button>
                                </Link>
                            </div>
                        </CardContent>
                    </Card>
                ) : (
                    <div className="space-y-4">
                        {posts.data.map((post) => (
                            <Card key={post.id} className="hover:shadow-md transition-shadow">
                                <CardHeader>
                                    <div className="flex items-start justify-between">
                                        <div className="flex-1">
                                            <CardTitle className="text-xl">
                                                <Link 
                                                    href={`/posts/${post.id}`}
                                                    className="hover:text-primary transition-colors"
                                                >
                                                    {post.name}
                                                </Link>
                                            </CardTitle>
                                            <div className="flex items-center gap-4 text-sm text-muted-foreground mt-2">
                                                <div className="flex items-center gap-1">
                                                    <User className="h-3 w-3" />
                                                    {post.user.name}
                                                </div>
                                                <div className="flex items-center gap-1">
                                                    <Clock className="h-3 w-3" />
                                                    {formatDate(post.created_at)}
                                                </div>
                                                <div className="flex items-center gap-1">
                                                    <MessageCircle className="h-3 w-3" />
                                                    {post.comments.length} comments
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </CardHeader>
                                <CardContent>
                                    <p className="text-muted-foreground line-clamp-3">
                                        {post.content.substring(0, 200)}
                                        {post.content.length > 200 && '...'}
                                    </p>
                                    <div className="flex gap-2 mt-4">
                                        <Link href={`/posts/${post.id}`}>
                                            <Button variant="outline" size="sm">
                                                Read More
                                            </Button>
                                        </Link>
                                        <Link href={`/posts/${post.id}/edit`}>
                                            <Button variant="outline" size="sm">
                                                Edit
                                            </Button>
                                        </Link>
                                    </div>
                                </CardContent>
                            </Card>
                        ))}

                        {/* Pagination */}
                        {posts.last_page > 1 && (
                            <div className="flex items-center justify-center gap-2 mt-8">
                                {posts.links.map((link, index) => (
                                    <span key={index}>
                                        {link.url ? (
                                            <Link href={link.url}>
                                                <Button
                                                    variant={link.active ? "default" : "outline"}
                                                    size="sm"
                                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                                />
                                            </Link>
                                        ) : (
                                            <Button
                                                variant="outline"
                                                size="sm"
                                                disabled
                                                dangerouslySetInnerHTML={{ __html: link.label }}
                                            />
                                        )}
                                    </span>
                                ))}
                            </div>
                        )}
                    </div>
                )}
            </div>
        </AppLayout>
    );
}