import { type BreadcrumbItem, type SharedData } from '@/types';
import { Head, Link, usePage, useForm } from '@inertiajs/react';
import { ArrowLeft, Edit, User, Clock, MessageCircle, Send } from 'lucide-react';
import { FormEventHandler } from 'react';

import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';

interface Comment {
    id: number;
    content: string;
    created_at: string;
    user: {
        id: number;
        name: string;
        email: string;
    };
}

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
    comments: Comment[];
}

interface PostShowProps {
    post: Post;
}

type CommentForm = {
    content: string;
    post_id: number;
};

export default function ShowPost({ post }: PostShowProps) {
    const { auth } = usePage<SharedData>().props;
    
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Posts',
            href: '/posts',
        },
        {
            title: post.name.length > 30 ? post.name.substring(0, 30) + '...' : post.name,
            href: `/posts/${post.id}`,
        },
    ];

    const { data, setData, post: submitComment, errors, processing, reset } = useForm<CommentForm>({
        content: '',
        post_id: post.id,
    });

    const submitCommentForm: FormEventHandler = (e) => {
        e.preventDefault();

        submitComment(route('comments.store'), {
            onSuccess: () => setData('content', ''),
        });
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

    const isOwner = auth.user.id === post.user.id;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={post.name} />
            
            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
                <div className="flex items-center gap-4">
                    <Link href="/posts">
                        <Button variant="outline" size="icon">
                            <ArrowLeft className="h-4 w-4" />
                        </Button>
                    </Link>
                    <div className="flex-1">
                        <h1 className="text-2xl font-semibold tracking-tight">{post.name}</h1>
                        <div className="flex items-center gap-4 text-sm text-muted-foreground mt-1">
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
                    {isOwner && (
                        <div className="flex gap-2">
                            <Link href={`/posts/${post.id}/edit`}>
                                <Button variant="outline" size="sm">
                                    <Edit className="h-4 w-4" />
                                    Edit
                                </Button>
                            </Link>
                        </div>
                    )}
                </div>

                <div className="grid gap-6 max-w-4xl">
                    {/* Post Content */}
                    <Card>
                        <CardContent className="pt-6">
                            <div className="prose prose-neutral dark:prose-invert max-w-none">
                                <div className="whitespace-pre-wrap text-foreground">
                                    {post.content}
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Comments Section */}
                    <div className="space-y-4">
                        <div className="flex items-center gap-2">
                            <h2 className="text-lg font-semibold">Comments</h2>
                            <span className="text-muted-foreground text-sm">({post.comments.length})</span>
                        </div>

                        {/* Add Comment Form */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="text-base">Add a comment</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <form onSubmit={submitCommentForm} className="space-y-4">
                                    <div className="space-y-2">
                                        <Label htmlFor="content">Comment</Label>
                                        <textarea
                                            id="content"
                                            value={data.content}
                                            onChange={(e) => setData('content', e.target.value)}
                                            placeholder="Write your comment..."
                                            rows={4}
                                            className="border-input file:text-foreground placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground flex w-full min-w-0 rounded-md border bg-transparent px-3 py-2 text-base shadow-xs transition-[color,box-shadow] outline-none disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive resize-none"
                                            aria-invalid={!!errors.content}
                                        />
                                        <InputError message={errors.content} />
                                    </div>

                                    <Button type="submit" disabled={processing} size="sm">
                                        <Send className="h-4 w-4" />
                                        {processing ? 'Posting...' : 'Post Comment'}
                                    </Button>
                                </form>
                            </CardContent>
                        </Card>

                        {/* Comments List */}
                        <div className="space-y-4">
                            {post.comments.length === 0 ? (
                                <Card>
                                    <CardContent className="text-center py-8">
                                        <div className="flex flex-col items-center gap-2">
                                            <MessageCircle className="h-8 w-8 text-muted-foreground" />
                                            <p className="text-muted-foreground">No comments yet. Be the first to comment!</p>
                                        </div>
                                    </CardContent>
                                </Card>
                            ) : (
                                post.comments.map((comment) => (
                                    <Card key={comment.id}>
                                        <CardContent className="pt-4">
                                            <div className="flex items-start gap-3">
                                                <div className="flex-1">
                                                    <div className="flex items-center gap-2 mb-2">
                                                        <span className="font-medium text-sm">{comment.user.name}</span>
                                                        <span className="text-muted-foreground text-xs">
                                                            {formatDate(comment.created_at)}
                                                        </span>
                                                    </div>
                                                    <p className="text-sm whitespace-pre-wrap">{comment.content}</p>
                                                </div>
                                            </div>
                                        </CardContent>
                                    </Card>
                                ))
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}