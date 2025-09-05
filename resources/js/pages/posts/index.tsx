import { type BreadcrumbItem, type SharedData } from '@/types';
import { Head, Link, router, usePage, WhenVisible } from '@inertiajs/react';
import { Clock, Edit, Eye, MessageCircle, Plus, Search, User, X } from 'lucide-react';
import { useEffect, useState } from 'react';

import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
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
    slug: string;
    content: string;
    category?: string;
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
    posts: Post[];
    filters: {
        search: string;
    };
    page: number;
    nextPageExists: boolean;
    total: number;
}


export default function PostsIndex({ posts, filters, page, nextPageExists, total }: PostsPageProps) {
    const { auth } = usePage<SharedData>().props;
    const [search, setSearch] = useState(filters.search || '');
    const [isSearching, setIsSearching] = useState(false);

    useEffect(() => {
        const timeoutId = setTimeout(() => {
            if (search !== filters.search) {
                setIsSearching(true);
                router.get(
                    '/posts',
                    { search: search || undefined },
                    {
                        preserveState: true,
                        preserveScroll: true,
                        onFinish: () => setIsSearching(false),
                    }
                );
            }
        }, 300); // 300ms debounce

        return () => clearTimeout(timeoutId);
    }, [search, filters.search]);

    const clearSearch = () => {
        setSearch('');
    };

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

            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-hidden rounded-xl p-4">
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

                {/* Search Bar */}
                <div className="relative max-w-md">
                    <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        type="text"
                        placeholder="Search posts by title, content, or category..."
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        className="pl-10 pr-10"
                    />
                    {search && (
                        <Button
                            variant="ghost"
                            size="sm"
                            onClick={clearSearch}
                            className="absolute right-1 top-1/2 h-8 w-8 -translate-y-1/2 p-0"
                        >
                            <X className="h-4 w-4" />
                        </Button>
                    )}
                    {isSearching && (
                        <div className="absolute right-3 top-1/2 -translate-y-1/2">
                            <div className="h-4 w-4 animate-spin rounded-full border-2 border-primary border-t-transparent" />
                        </div>
                    )}
                </div>

                {posts.length === 0 ? (
                    <Card className="py-12 text-center">
                        <CardContent>
                            <div className="flex flex-col items-center gap-4">
                                <div className="bg-muted flex h-12 w-12 items-center justify-center rounded-full">
                                    {search ? (
                                        <Search className="text-muted-foreground h-6 w-6" />
                                    ) : (
                                        <Plus className="text-muted-foreground h-6 w-6" />
                                    )}
                                </div>
                                <div>
                                    <h3 className="text-lg font-semibold">
                                        {search ? 'No posts found' : 'No posts yet'}
                                    </h3>
                                    <p className="text-muted-foreground text-sm">
                                        {search
                                            ? `No posts match "${search}". Try adjusting your search terms.`
                                            : 'Create your first blog post to get started.'}
                                    </p>
                                </div>
                                {search ? (
                                    <Button variant="outline" onClick={clearSearch}>
                                        Clear Search
                                    </Button>
                                ) : (
                                    <Link href="/posts/create">
                                        <Button>Create Post</Button>
                                    </Link>
                                )}
                            </div>
                        </CardContent>
                    </Card>
                ) : (
                    <div className="space-y-4">
                        <Card className="overflow-hidden">
                            {search && (
                                <div className="border-b bg-muted/30 px-4 py-2">
                                    <p className="text-sm text-muted-foreground">
                                        Found {total} result{total !== 1 ? 's' : ''} for "{search}"
                                    </p>
                                </div>
                            )}
                            <Table className="table-fixed">
                                <TableHeader>
                                    <TableRow>
                                        <TableHead className="w-2/5">Title</TableHead>
                                        <TableHead className="w-1/6">Category</TableHead>
                                        <TableHead className="w-1/6">Author</TableHead>
                                        <TableHead className="w-1/6">Created</TableHead>
                                        <TableHead className="w-1/12">Comments</TableHead>
                                        <TableHead className="w-1/12">Actions</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {posts.map((post) => (
                                        <TableRow key={post.id}>
                                            <TableCell className="whitespace-normal">
                                                <div>
                                                    <Link
                                                        href={`/posts/${post.slug}`}
                                                        className="hover:text-primary block truncate font-medium transition-colors"
                                                        title={post.name}
                                                    >
                                                        {post.name}
                                                    </Link>
                                                    <p className="text-muted-foreground mt-1 truncate text-sm">
                                                        {post.content.substring(0, 80)}
                                                        {post.content.length > 80 && '...'}
                                                    </p>
                                                </div>
                                            </TableCell>
                                            <TableCell>
                                                <span className="text-sm">{post.category || 'Uncategorized'}</span>
                                            </TableCell>
                                            <TableCell>
                                                <div className="flex items-center gap-1">
                                                    <User className="h-3 w-3 flex-shrink-0" />
                                                    <span className="truncate" title={post.user.name}>
                                                        {post.user.name}
                                                    </span>
                                                </div>
                                            </TableCell>
                                            <TableCell>
                                                <div className="flex items-center gap-1">
                                                    <Clock className="h-3 w-3 flex-shrink-0" />
                                                    <span className="truncate text-sm">{formatDate(post.created_at)}</span>
                                                </div>
                                            </TableCell>
                                            <TableCell className="text-center">
                                                <div className="flex items-center justify-center gap-1">
                                                    <MessageCircle className="h-3 w-3 flex-shrink-0" />
                                                    <span>{post.comments.length}</span>
                                                </div>
                                            </TableCell>
                                            <TableCell>
                                                <div className="flex gap-1">
                                                    <Link href={`/posts/${post.slug}`}>
                                                        <Button variant="ghost" size="sm">
                                                            <Eye className="h-4 w-4" />
                                                        </Button>
                                                    </Link>
                                                    {auth.user.id === post.user.id && (
                                                        <Link href={`/posts/${post.slug}/edit`}>
                                                            <Button variant="ghost" size="sm">
                                                                <Edit className="h-4 w-4" />
                                                            </Button>
                                                        </Link>
                                                    )}
                                                </div>
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                    {nextPageExists && (
                                        <WhenVisible
                                            always
                                            params={{
                                                data: {
                                                    page: + page + 1,
                                                },
                                                only: ['posts', 'page', 'isNextPageExists'],
                                            }}
                                            fallback={<p>You reach the end.</p>}
                                        >
                                            <p>Loading...</p>
                                        </WhenVisible>
                                    )}
                                </TableBody>
                            </Table>
                        </Card>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
